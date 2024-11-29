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

use Fresh\CentrifugoBundle\Exception\CentrifugoErrorException;
use Fresh\CentrifugoBundle\Exception\CentrifugoException;
use Fresh\CentrifugoBundle\Exception\LogicException;
use Fresh\CentrifugoBundle\Logger\CommandHistoryLogger;
use Fresh\CentrifugoBundle\Model\BatchRequest;
use Fresh\CentrifugoBundle\Model\BroadcastCommand;
use Fresh\CentrifugoBundle\Model\ChannelsCommand;
use Fresh\CentrifugoBundle\Model\CommandInterface;
use Fresh\CentrifugoBundle\Model\PublishCommand;
use Fresh\CentrifugoBundle\Model\ResultableCommandInterface;
use Fresh\CentrifugoBundle\Service\CentrifugoChecker;
use Fresh\CentrifugoBundle\Service\ResponseProcessor;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SEEC\PhpUnit\Helper\ConsecutiveParams;
use Symfony\Component\HttpKernel\Profiler\Profiler;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * ResponseProcessorTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class ResponseProcessorTest extends TestCase
{
    use ConsecutiveParams;

    /** @var ResponseInterface|MockObject */
    private ResponseInterface|MockObject $response;

    /** @var CentrifugoChecker|MockObject */
    private CentrifugoChecker|MockObject $centrifugoChecker;

    /** @var CommandHistoryLogger|MockObject */
    private CommandHistoryLogger|MockObject $commandHistoryLogger;

    /** @var Profiler|MockObject */
    private Profiler|MockObject $profiler;

    private ResponseProcessor $responseProcessor;

    protected function setUp(): void
    {
        $this->response = $this->createMock(ResponseInterface::class);
        $this->commandHistoryLogger = $this->createMock(CommandHistoryLogger::class);
        $this->profiler = $this->createMock(Profiler::class);
        $this->centrifugoChecker = $this->createMock(CentrifugoChecker::class);
        $this->responseProcessor = new ResponseProcessor(
            $this->centrifugoChecker,
            $this->commandHistoryLogger,
            $this->profiler
        );
    }

    protected function tearDown(): void
    {
        unset(
            $this->response,
            $this->centrifugoChecker,
            $this->commandHistoryLogger,
            $this->profiler,
            $this->responseProcessor,
        );
    }

    #[Test]
    public function processingBatchRequest(): void
    {
        $this->centrifugoChecker
            ->expects($this->once())
            ->method('assertValidResponseStatusCode')
            ->with($this->response)
        ;
        $this->centrifugoChecker
            ->expects($this->once())
            ->method('assertValidResponseHeaders')
            ->with($this->response)
        ;
        $this->centrifugoChecker
            ->expects($this->once())
            ->method('assertValidResponseContentType')
            ->with($this->response)
        ;

        $this->response
            ->expects($this->once())
            ->method('getContent')
            ->willReturn(<<<'JSON'
                {
                    "replies": [
                        {"publish": {}},
                        {"broadcast": {}},
                        ["chat", "notification"]
                    ]
                }
            JSON
            )
        ;

        $this->commandHistoryLogger
            ->expects($this->exactly(3))
            ->method('logCommand')
            ->with(
                ...$this->withConsecutive(
                    [$this->isInstanceOf(PublishCommand::class), true, null],
                    [$this->isInstanceOf(BroadcastCommand::class), true, null],
                    [$this->isInstanceOf(ChannelsCommand::class), true, ['chat', 'notification']],
                )
            )
        ;

        $result = $this->responseProcessor->processResponse(
            new BatchRequest(
                [
                    new PublishCommand(['foo' => 'bar'], 'channelA'),
                    new BroadcastCommand(['foo' => 'bar'], ['channelA', 'channelB']),
                    new ChannelsCommand(),
                ]
            ),
            $this->response
        );
        $this->assertSame(
            [
                null,
                null,
                ['chat', 'notification'],
            ],
            $result
        );
    }

    #[Test]
    public function logicException(): void
    {
        $this->response
            ->expects($this->once())
            ->method('getContent')
            ->willReturn(<<<'JSON'
                {
                    "replies": {
                        "result":["chat","notification"]
                    }
                }
            JSON
            )
        ;

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Number of commands doesn\'t match number of responses');

        $this->responseProcessor->processResponse(
            new BatchRequest(
                [
                    new PublishCommand(['foo' => 'bar'], 'channelA'),
                    new BroadcastCommand(['foo' => 'bar'], ['channelA', 'channelB']),
                    new ChannelsCommand(),
                ]
            ),
            $this->response
        );
    }

    #[Test]
    public function processingResultableCommand(): void
    {
        $this->centrifugoChecker
            ->expects($this->once())
            ->method('assertValidResponseStatusCode')
            ->with($this->response)
        ;
        $this->centrifugoChecker
            ->expects($this->once())
            ->method('assertValidResponseHeaders')
            ->with($this->response)
        ;
        $this->centrifugoChecker
            ->expects($this->once())
            ->method('assertValidResponseContentType')
            ->with($this->response)
        ;

        $this->response
            ->expects($this->once())
            ->method('getContent')
            ->willReturn(<<<'JSON'
                ["foo", "bar"]
            JSON
            )
        ;

        $command = new ChannelsCommand();

        $this->commandHistoryLogger
            ->expects($this->once())
            ->method('logCommand')
            ->with($command, true, ['foo', 'bar'])
        ;

        $result = $this->responseProcessor->processResponse($command, $this->response);
        $this->assertSame(['foo', 'bar'], $result);
    }

    #[Test]
    public function processingNonResultableCommand(): void
    {
        $this->centrifugoChecker
            ->expects($this->once())
            ->method('assertValidResponseStatusCode')
            ->with($this->response)
        ;
        $this->centrifugoChecker
            ->expects($this->once())
            ->method('assertValidResponseHeaders')
            ->with($this->response)
        ;
        $this->centrifugoChecker
            ->expects($this->once())
            ->method('assertValidResponseContentType')
            ->with($this->response)
        ;

        $this->response
            ->expects($this->once())
            ->method('getContent')
            ->willReturn(<<<'JSON'
                {
                    "replies": [
                        {
                            "publish": {}
                        }
                    ]
                }
            JSON
            )
        ;

        $command = new PublishCommand(['foo' => 'bar'], 'test');

        $this->commandHistoryLogger
            ->expects($this->once())
            ->method('logCommand')
            ->with($command, true, null)
        ;

        $result = $this->responseProcessor->processResponse($command, $this->response);
        $this->assertNull($result);
    }

    #[Test]
    public function invalidResponse(): void
    {
        $this->response
            ->expects($this->once())
            ->method('getContent')
            ->willReturn('invalid json')
        ;

        $this->expectException(CentrifugoException::class);
        $this->expectExceptionMessage('Centrifugo response payload is not a valid JSON');

        $this->responseProcessor->processResponse(
            $this->createStub(ResultableCommandInterface::class),
            $this->response
        );
    }

    #[Test]
    public function processingCentrifugoErrorForSingleCommand(): void
    {
        $this->response
            ->expects($this->once())
            ->method('getContent')
            ->willReturn('{"error":{"message":"test message","code":123}}')
        ;

        $command = $this->createStub(CommandInterface::class);

        $this->commandHistoryLogger
            ->expects($this->once())
            ->method('logCommand')
            ->with($command, false, ['error' => ['message' => 'test message', 'code' => '123']])
        ;

        $this->expectException(CentrifugoErrorException::class);
        $this->expectExceptionCode(123);
        $this->expectExceptionMessage('test message');

        $this->responseProcessor->processResponse($command, $this->response);
    }

    #[Test]
    public function processingCentrifugoErrorForBatchRequest(): void
    {
        $this->response
            ->expects($this->once())
            ->method('getContent')
            ->willReturn(<<<'JSON'
                {
                    "replies": [
                        {"error":{"message":"test message 2","code":456}},
                        {"broadcast":{}},
                        ["chat","notification"]
                    ]
                }
            JSON
            )
        ;

        $this->commandHistoryLogger
            ->expects($this->exactly(3))
            ->method('logCommand')
            ->with(
                ...$this->withConsecutive(
                    [$this->isInstanceOf(PublishCommand::class), false, ['error' => ['message' => 'test message 2', 'code' => 456]]],
                    [$this->isInstanceOf(BroadcastCommand::class), true, null],
                    [$this->isInstanceOf(ChannelsCommand::class), true, ['chat', 'notification']],
                )
            )
        ;

        $this->expectException(CentrifugoErrorException::class);
        $this->expectExceptionCode(456);
        $this->expectExceptionMessage('test message 2');

        $this->responseProcessor->processResponse(
            new BatchRequest(
                [
                    new PublishCommand(['foo' => 'bar'], 'channelA'),
                    new BroadcastCommand(['foo' => 'bar'], ['channelA', 'channelB']),
                    new ChannelsCommand(),
                ]
            ),
            $this->response
        );
    }
}
