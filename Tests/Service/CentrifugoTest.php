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
    private $httpClient;

    /** @var ResponseInterface|MockObject */
    private $response;

    /** @var ResponseProcessor|MockObject */
    private $responseProcessor;

    /** @var CommandHistoryLogger|MockObject */
    private $commandHistoryLogger;

    /** @var CentrifugoChecker|MockObject */
    private $centrifugoChecker;

    /** @var Profiler|MockObject */
    private $profiler;

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

        $this->centrifugo->publish(['foo' => 'bar'], 'channelA');
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

        $this->centrifugo->broadcast(['foo' => 'bar'], ['channelA', 'channelB']);
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

        $this->centrifugo->unsubscribe('user123', 'channelA');
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

        $this->centrifugo->disconnect('user123');
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

        $this->centrifugo->presence('channelA');
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

        $this->centrifugo->presenceStats('channelA');
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

        $this->centrifugo->history('channelA');
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

        $this->centrifugo->historyRemove('channelA');
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

        $this->centrifugo->channels('pattern');
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
