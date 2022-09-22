<?php
/*
 * This file is part of the FreshCentrifugoBundle.
 *
 * (c) Artem Henvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Fresh\CentrifugoBundle\Tests\Service;

use Fresh\CentrifugoBundle\Logger\CommandHistoryLogger;
use Fresh\CentrifugoBundle\Model;
use Fresh\CentrifugoBundle\Model\Override;
use Fresh\CentrifugoBundle\Model\StreamPosition;
use Fresh\CentrifugoBundle\Service\Centrifugo;
use Fresh\CentrifugoBundle\Service\CentrifugoChecker;
use Fresh\CentrifugoBundle\Service\ResponseProcessor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Profiler\Profiler;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * CentrifugoTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class CentrifugoTest extends TestCase
{
    /** @var HttpClientInterface|MockObject */
    private HttpClientInterface|MockObject $httpClient;

    /** @var ResponseInterface|MockObject */
    private ResponseInterface|MockObject $response;

    /** @var ResponseProcessor|MockObject */
    private ResponseProcessor|MockObject $responseProcessor;

    /** @var CommandHistoryLogger|MockObject */
    private CommandHistoryLogger|MockObject $commandHistoryLogger;

    /** @var CentrifugoChecker|MockObject */
    private CentrifugoChecker|MockObject $centrifugoChecker;

    /** @var Profiler|MockObject */
    private Profiler|MockObject $profiler;

    private Centrifugo $centrifugo;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);
        $this->responseProcessor = $this->createMock(ResponseProcessor::class);
        $this->commandHistoryLogger = $this->createMock(CommandHistoryLogger::class);
        $this->centrifugoChecker = $this->createMock(CentrifugoChecker::class);
        $this->profiler = $this->createMock(Profiler::class);
        $this->centrifugo = new Centrifugo(
            'http://test.com',
            'qwerty',
            $this->httpClient,
            $this->responseProcessor,
            $this->commandHistoryLogger,
            $this->centrifugoChecker,
            $this->profiler
        );
    }

    protected function tearDown(): void
    {
        unset(
            $this->response,
            $this->httpClient,
            $this->responseProcessor,
            $this->commandHistoryLogger,
            $this->centrifugoChecker,
            $this->profiler,
            $this->centrifugo,
        );
    }

    public function testPublishCommand(): void
    {
        $this->centrifugoChecker
            ->expects(self::once())
            ->method('assertValidChannelName')
            ->with('channelA')
        ;

        $this->httpClient
            ->expects(self::once())
            ->method('request')
            ->willReturn($this->response)
        ;

        $this->commandHistoryLogger
            ->expects(self::once())
            ->method('increaseRequestsCount')
        ;

        $this->responseProcessor
            ->expects(self::once())
            ->method('processResponse')
            ->with(self::isInstanceOf(Model\PublishCommand::class), $this->response)
            ->willReturn(null)
        ;

        $this->centrifugo->publish(
            data: ['foo' => 'bar'],
            channel: 'channelA',
            skipHistory: true,
            tags: ['tag' => 'value'],
            base64data: 'SGVsbG8gd29ybGQ=',
        );
    }

    public function testBroadcastCommand(): void
    {
        $this->centrifugoChecker
            ->expects(self::exactly(2))
            ->method('assertValidChannelName')
            ->withConsecutive(['channelA'], ['channelB'])
        ;

        $this->httpClient
            ->expects(self::once())
            ->method('request')
            ->willReturn($this->response)
        ;

        $this->commandHistoryLogger
            ->expects(self::once())
            ->method('increaseRequestsCount')
        ;

        $this->responseProcessor
            ->expects(self::once())
            ->method('processResponse')
            ->with(self::isInstanceOf(Model\BroadcastCommand::class), $this->response)
            ->willReturn(null)
        ;

        $this->centrifugo->broadcast(
            data: ['foo' => 'bar'],
            channels: ['channelA', 'channelB'],
            skipHistory: true,
            tags: ['tag' => 'value'],
            base64data: 'SGVsbG8gd29ybGQ=',
        );
    }

    public function testUnsubscribeCommand(): void
    {
        $this->centrifugoChecker
            ->expects(self::once())
            ->method('assertValidChannelName')
            ->with('channelA')
        ;

        $this->httpClient
            ->expects(self::once())
            ->method('request')
            ->willReturn($this->response)
        ;

        $this->commandHistoryLogger
            ->expects(self::once())
            ->method('increaseRequestsCount')
        ;

        $this->responseProcessor
            ->expects(self::once())
            ->method('processResponse')
            ->with(self::isInstanceOf(Model\UnsubscribeCommand::class), $this->response)
            ->willReturn(null)
        ;

        $this->centrifugo->unsubscribe(
            user: 'user123',
            channel: 'channelA',
            client: 'client',
            session: 'session',
        );
    }

    public function testSubscribeCommand(): void
    {
        $this->centrifugoChecker
            ->expects(self::once())
            ->method('assertValidChannelName')
            ->with('channelA')
        ;

        $this->httpClient
            ->expects(self::once())
            ->method('request')
            ->willReturn($this->response)
        ;

        $this->commandHistoryLogger
            ->expects(self::once())
            ->method('increaseRequestsCount')
        ;

        $this->responseProcessor
            ->expects(self::once())
            ->method('processResponse')
            ->with(self::isInstanceOf(Model\SubscribeCommand::class), $this->response)
            ->willReturn(null)
        ;

        $this->centrifugo->subscribe(
            user: 'user123',
            channel: 'channelA',
            info: ['foo' => 'bar'],
            base64Info: 'qwerty',
            client: 'clientID',
            session: 'sessionID',
            data: ['abc' => 'def'],
            base64Data: '12345',
            recoverSince: new StreamPosition(offset: 5, epoch: 'test'),
            override: new Override(
                presence: true,
                joinLeave: false,
                forcePushJoinLeave: true,
                forcePositioning: false,
                forceRecovery: true,
            ),
        );
    }

    public function testDisconnectCommand(): void
    {
        $this->centrifugoChecker
            ->expects(self::never())
            ->method('assertValidChannelName')
        ;

        $this->httpClient
            ->expects(self::once())
            ->method('request')
            ->willReturn($this->response)
        ;

        $this->commandHistoryLogger
            ->expects(self::once())
            ->method('increaseRequestsCount')
        ;

        $this->responseProcessor
            ->expects(self::once())
            ->method('processResponse')
            ->with(self::isInstanceOf(Model\DisconnectCommand::class), $this->response)
            ->willReturn(null)
        ;

        $this->centrifugo->disconnect(
            user: 'user123',
            whitelist: ['test'],
            client: 'test',
            session: 'test',
            disconnectObject: new Model\Disconnect(999, 'test'),
        );
    }

    public function testRefreshCommand(): void
    {
        $this->centrifugoChecker
            ->expects(self::never())
            ->method('assertValidChannelName')
        ;

        $this->httpClient
            ->expects(self::once())
            ->method('request')
            ->willReturn($this->response)
        ;

        $this->commandHistoryLogger
            ->expects(self::once())
            ->method('increaseRequestsCount')
        ;

        $this->responseProcessor
            ->expects(self::once())
            ->method('processResponse')
            ->with(self::isInstanceOf(Model\RefreshCommand::class), $this->response)
            ->willReturn(null)
        ;

        $this->centrifugo->refresh(
            user: 'user123',
            client: 'test',
            session: 'test',
            expired: true,
            expireAt: 1234567890,
        );
    }

    public function testPresenceCommand(): void
    {
        $this->centrifugoChecker
            ->expects(self::once())
            ->method('assertValidChannelName')
            ->with('channelA')
        ;

        $this->httpClient
            ->expects(self::once())
            ->method('request')
            ->willReturn($this->response)
        ;

        $this->commandHistoryLogger
            ->expects(self::once())
            ->method('increaseRequestsCount')
        ;

        $this->responseProcessor
            ->expects(self::once())
            ->method('processResponse')
            ->with(self::isInstanceOf(Model\PresenceCommand::class), $this->response)
            ->willReturn([])
        ;

        $this->centrifugo->presence(channel: 'channelA');
    }

    public function testPresenceStatsCommand(): void
    {
        $this->centrifugoChecker
            ->expects(self::once())
            ->method('assertValidChannelName')
            ->with('channelA')
        ;

        $this->httpClient
            ->expects(self::once())
            ->method('request')
            ->willReturn($this->response)
        ;

        $this->commandHistoryLogger
            ->expects(self::once())
            ->method('increaseRequestsCount')
        ;

        $this->responseProcessor
            ->expects(self::once())
            ->method('processResponse')
            ->with(self::isInstanceOf(Model\PresenceStatsCommand::class), $this->response)
            ->willReturn([])
        ;

        $this->centrifugo->presenceStats(channel: 'channelA');
    }

    public function testHistoryCommand(): void
    {
        $this->centrifugoChecker
            ->expects(self::once())
            ->method('assertValidChannelName')
            ->with('channelA')
        ;

        $this->httpClient
            ->expects(self::once())
            ->method('request')
            ->willReturn($this->response)
        ;

        $this->commandHistoryLogger
            ->expects(self::once())
            ->method('increaseRequestsCount')
        ;

        $this->responseProcessor
            ->expects(self::once())
            ->method('processResponse')
            ->with(self::isInstanceOf(Model\HistoryCommand::class), $this->response)
            ->willReturn([])
        ;

        $this->centrifugo->history(
            channel: 'channelA',
            reverse: true,
            limit: 10,
            streamPosition: new StreamPosition(5, 'ABCD'),
        );
    }

    public function testHistoryRemoveCommand(): void
    {
        $this->centrifugoChecker
            ->expects(self::once())
            ->method('assertValidChannelName')
            ->with('channelA')
        ;

        $this->httpClient
            ->expects(self::once())
            ->method('request')
            ->willReturn($this->response)
        ;

        $this->commandHistoryLogger
            ->expects(self::once())
            ->method('increaseRequestsCount')
        ;

        $this->responseProcessor
            ->expects(self::once())
            ->method('processResponse')
            ->with(self::isInstanceOf(Model\HistoryRemoveCommand::class), $this->response)
            ->willReturn(null)
        ;

        $this->centrifugo->historyRemove(channel: 'channelA');
    }

    public function testChannelsCommand(): void
    {
        $this->centrifugoChecker
            ->expects(self::never())
            ->method('assertValidChannelName')
        ;

        $this->httpClient
            ->expects(self::once())
            ->method('request')
            ->willReturn($this->response)
        ;

        $this->commandHistoryLogger
            ->expects(self::once())
            ->method('increaseRequestsCount')
        ;

        $this->responseProcessor
            ->expects(self::once())
            ->method('processResponse')
            ->with(self::isInstanceOf(Model\ChannelsCommand::class), $this->response)
            ->willReturn([])
        ;

        $this->centrifugo->channels(pattern: 'pattern');
    }

    public function testInfoCommand(): void
    {
        $this->centrifugoChecker
            ->expects(self::never())
            ->method('assertValidChannelName')
        ;

        $this->httpClient
            ->expects(self::once())
            ->method('request')
            ->willReturn($this->response)
        ;

        $this->commandHistoryLogger
            ->expects(self::once())
            ->method('increaseRequestsCount')
        ;

        $this->responseProcessor
            ->expects(self::once())
            ->method('processResponse')
            ->with(self::isInstanceOf(Model\InfoCommand::class), $this->response)
            ->willReturn([])
        ;

        $this->centrifugo->info();
    }

    public function testBatchRequest(): void
    {
        $this->centrifugoChecker
            ->expects(self::exactly(2))
            ->method('assertValidChannelName')
            ->withConsecutive(['channelA'], ['channelB'])
        ;

        $this->httpClient
            ->expects(self::once())
            ->method('request')
            ->willReturn($this->response)
        ;

        $this->commandHistoryLogger
            ->expects(self::once())
            ->method('increaseRequestsCount')
        ;

        $this->responseProcessor
            ->expects(self::once())
            ->method('processResponse')
            ->with(self::isInstanceOf(Model\BatchRequest::class), $this->response)
            ->willReturn([])
        ;

        $this->centrifugo->batchRequest(
            [
                new Model\PublishCommand([], 'channelA'),
                new Model\PublishCommand([], 'channelB'),
            ]
        );
    }
}
