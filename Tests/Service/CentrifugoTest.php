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
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SEEC\PhpUnit\Helper\ConsecutiveParams;
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
    use ConsecutiveParams;

    private HttpClientInterface|MockObject $httpClient;
    private ResponseInterface|MockObject $response;
    private ResponseProcessor|MockObject $responseProcessor;
    private CommandHistoryLogger|MockObject $commandHistoryLogger;
    private CentrifugoChecker|MockObject $centrifugoChecker;
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
            $this->profiler,
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

    #[Test]
    public function publishCommand(): void
    {
        $this->centrifugoChecker
            ->expects($this->once())
            ->method('assertValidChannelName')
            ->with('channelA')
        ;

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->willReturn($this->response)
        ;

        $this->commandHistoryLogger
            ->expects($this->once())
            ->method('increaseRequestsCount')
        ;

        $this->responseProcessor
            ->expects($this->once())
            ->method('processResponse')
            ->with($this->isInstanceOf(Model\PublishCommand::class), $this->response)
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

    #[Test]
    public function broadcastCommand(): void
    {
        $this->centrifugoChecker
            ->expects($this->exactly(2))
            ->method('assertValidChannelName')
            ->with(...self::withConsecutive(
                ['channelA'],
                ['channelB'],
            ))
        ;

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->willReturn($this->response)
        ;

        $this->commandHistoryLogger
            ->expects($this->once())
            ->method('increaseRequestsCount')
        ;

        $this->responseProcessor
            ->expects($this->once())
            ->method('processResponse')
            ->with($this->isInstanceOf(Model\BroadcastCommand::class), $this->response)
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

    #[Test]
    public function unsubscribeCommand(): void
    {
        $this->centrifugoChecker
            ->expects($this->once())
            ->method('assertValidChannelName')
            ->with('channelA')
        ;

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->willReturn($this->response)
        ;

        $this->commandHistoryLogger
            ->expects($this->once())
            ->method('increaseRequestsCount')
        ;

        $this->responseProcessor
            ->expects($this->once())
            ->method('processResponse')
            ->with($this->isInstanceOf(Model\UnsubscribeCommand::class), $this->response)
            ->willReturn(null)
        ;

        $this->centrifugo->unsubscribe(
            user: 'user123',
            channel: 'channelA',
            client: 'client',
            session: 'session',
        );
    }

    #[Test]
    public function subscribeCommand(): void
    {
        $this->centrifugoChecker
            ->expects($this->once())
            ->method('assertValidChannelName')
            ->with('channelA')
        ;

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->willReturn($this->response)
        ;

        $this->commandHistoryLogger
            ->expects($this->once())
            ->method('increaseRequestsCount')
        ;

        $this->responseProcessor
            ->expects($this->once())
            ->method('processResponse')
            ->with($this->isInstanceOf(Model\SubscribeCommand::class), $this->response)
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

    #[Test]
    public function disconnectCommand(): void
    {
        $this->centrifugoChecker
            ->expects($this->never())
            ->method('assertValidChannelName')
        ;

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->willReturn($this->response)
        ;

        $this->commandHistoryLogger
            ->expects($this->once())
            ->method('increaseRequestsCount')
        ;

        $this->responseProcessor
            ->expects($this->once())
            ->method('processResponse')
            ->with($this->isInstanceOf(Model\DisconnectCommand::class), $this->response)
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

    #[Test]
    public function refreshCommand(): void
    {
        $this->centrifugoChecker
            ->expects($this->never())
            ->method('assertValidChannelName')
        ;

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->willReturn($this->response)
        ;

        $this->commandHistoryLogger
            ->expects($this->once())
            ->method('increaseRequestsCount')
        ;

        $this->responseProcessor
            ->expects($this->once())
            ->method('processResponse')
            ->with($this->isInstanceOf(Model\RefreshCommand::class), $this->response)
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

    #[Test]
    public function presenceCommand(): void
    {
        $this->centrifugoChecker
            ->expects($this->once())
            ->method('assertValidChannelName')
            ->with('channelA')
        ;

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->willReturn($this->response)
        ;

        $this->commandHistoryLogger
            ->expects($this->once())
            ->method('increaseRequestsCount')
        ;

        $this->responseProcessor
            ->expects($this->once())
            ->method('processResponse')
            ->with($this->isInstanceOf(Model\PresenceCommand::class), $this->response)
            ->willReturn([])
        ;

        $this->centrifugo->presence(channel: 'channelA');
    }

    #[Test]
    public function presenceStatsCommand(): void
    {
        $this->centrifugoChecker
            ->expects($this->once())
            ->method('assertValidChannelName')
            ->with('channelA')
        ;

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->willReturn($this->response)
        ;

        $this->commandHistoryLogger
            ->expects($this->once())
            ->method('increaseRequestsCount')
        ;

        $this->responseProcessor
            ->expects($this->once())
            ->method('processResponse')
            ->with($this->isInstanceOf(Model\PresenceStatsCommand::class), $this->response)
            ->willReturn([])
        ;

        $this->centrifugo->presenceStats(channel: 'channelA');
    }

    #[Test]
    public function historyCommand(): void
    {
        $this->centrifugoChecker
            ->expects($this->once())
            ->method('assertValidChannelName')
            ->with('channelA')
        ;

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->willReturn($this->response)
        ;

        $this->commandHistoryLogger
            ->expects($this->once())
            ->method('increaseRequestsCount')
        ;

        $this->responseProcessor
            ->expects($this->once())
            ->method('processResponse')
            ->with($this->isInstanceOf(Model\HistoryCommand::class), $this->response)
            ->willReturn([])
        ;

        $this->centrifugo->history(
            channel: 'channelA',
            reverse: true,
            limit: 10,
            streamPosition: new StreamPosition(5, 'ABCD'),
        );
    }

    #[Test]
    public function historyRemoveCommand(): void
    {
        $this->centrifugoChecker
            ->expects($this->once())
            ->method('assertValidChannelName')
            ->with('channelA')
        ;

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->willReturn($this->response)
        ;

        $this->commandHistoryLogger
            ->expects($this->once())
            ->method('increaseRequestsCount')
        ;

        $this->responseProcessor
            ->expects($this->once())
            ->method('processResponse')
            ->with($this->isInstanceOf(Model\HistoryRemoveCommand::class), $this->response)
            ->willReturn(null)
        ;

        $this->centrifugo->historyRemove(channel: 'channelA');
    }

    #[Test]
    public function channelsCommand(): void
    {
        $this->centrifugoChecker
            ->expects($this->never())
            ->method('assertValidChannelName')
        ;

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->willReturn($this->response)
        ;

        $this->commandHistoryLogger
            ->expects($this->once())
            ->method('increaseRequestsCount')
        ;

        $this->responseProcessor
            ->expects($this->once())
            ->method('processResponse')
            ->with($this->isInstanceOf(Model\ChannelsCommand::class), $this->response)
            ->willReturn([])
        ;

        $this->centrifugo->channels(pattern: 'pattern');
    }

    #[Test]
    public function infoCommand(): void
    {
        $this->centrifugoChecker
            ->expects($this->never())
            ->method('assertValidChannelName')
        ;

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->willReturn($this->response)
        ;

        $this->commandHistoryLogger
            ->expects($this->once())
            ->method('increaseRequestsCount')
        ;

        $this->responseProcessor
            ->expects($this->once())
            ->method('processResponse')
            ->with($this->isInstanceOf(Model\InfoCommand::class), $this->response)
            ->willReturn([])
        ;

        $this->centrifugo->info();
    }

    #[Test]
    public function batchRequest(): void
    {
        $this->centrifugoChecker
            ->expects($this->exactly(2))
            ->method('assertValidChannelName')
            ->with(
                ...self::withConsecutive(
                    ['channelA'],
                    ['channelB'],
                )
            )
        ;

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->willReturn($this->response)
        ;

        $this->commandHistoryLogger
            ->expects($this->once())
            ->method('increaseRequestsCount')
        ;

        $this->responseProcessor
            ->expects($this->once())
            ->method('processResponse')
            ->with($this->isInstanceOf(Model\BatchRequest::class), $this->response)
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
