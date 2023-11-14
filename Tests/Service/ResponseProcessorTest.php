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
use Fresh\CentrifugoBundle\Tests\ConsecutiveParamsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Profiler\Profiler;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * ResponseProcessorTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class ResponseProcessorTest extends TestCase
{
    use ConsecutiveParamsTrait;

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

    public function testProcessingBatchRequest(): void
    {
        $this->centrifugoChecker
            ->expects(self::once())
            ->method('assertValidResponseStatusCode')
            ->with($this->response)
        ;
        $this->centrifugoChecker
            ->expects(self::once())
            ->method('assertValidResponseHeaders')
            ->with($this->response)
        ;
        $this->centrifugoChecker
            ->expects(self::once())
            ->method('assertValidResponseContentType')
            ->with($this->response)
        ;

        $this->response
            ->expects(self::once())
            ->method('getContent')
            ->willReturn(<<<'LDJSON'
                null
                null
                {"result":{"channels":["chat","notification"]}}
            LDJSON
            )
        ;

        $commandA = new PublishCommand(['foo' => 'bar'], 'channelA');
        $commandB = new BroadcastCommand(['foo' => 'bar'], ['channelA', 'channelB']);
        $commandC = new ChannelsCommand();

        $this->commandHistoryLogger
            ->expects(self::exactly(3))
            ->method('logCommand')
            ->with(
                ...$this->consecutiveParams(
                    [$commandA, true, null],
                    [$commandB, true, null],
                    [$commandC, true, ['channels' => ['chat', 'notification']]],
                )
            )
        ;

        $result = $this->responseProcessor->processResponse(
            new BatchRequest(
                [
                    $commandA,
                    $commandB,
                    $commandC,
                ]
            ),
            $this->response
        );
        self::assertSame(
            [
                null,
                null,
                [
                    'channels' => ['chat', 'notification'],
                ],
            ],
            $result
        );
    }

    public function testLogicException(): void
    {
        $this->response
            ->expects(self::once())
            ->method('getContent')
            ->willReturn(<<<'LDJSON'
                {"result":{"channels":["chat","notification"]}}
            LDJSON
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

    public function testProcessingResultableCommand(): void
    {
        $this->centrifugoChecker
            ->expects(self::once())
            ->method('assertValidResponseStatusCode')
            ->with($this->response)
        ;
        $this->centrifugoChecker
            ->expects(self::once())
            ->method('assertValidResponseHeaders')
            ->with($this->response)
        ;
        $this->centrifugoChecker
            ->expects(self::once())
            ->method('assertValidResponseContentType')
            ->with($this->response)
        ;

        $this->response
            ->expects(self::once())
            ->method('getContent')
            ->willReturn(<<<'JSON'
                {
                    "result": {
                        "foo": "bar"
                    }
                }
            JSON
            )
        ;

        $command = $this->createStub(ResultableCommandInterface::class);

        $this->commandHistoryLogger
            ->expects(self::once())
            ->method('logCommand')
            ->with($command, true, ['foo' => 'bar'])
        ;

        $result = $this->responseProcessor->processResponse($command, $this->response);
        self::assertSame(['foo' => 'bar'], $result);
    }

    public function testProcessingNonResultableCommand(): void
    {
        $this->centrifugoChecker
            ->expects(self::once())
            ->method('assertValidResponseStatusCode')
            ->with($this->response)
        ;
        $this->centrifugoChecker
            ->expects(self::once())
            ->method('assertValidResponseHeaders')
            ->with($this->response)
        ;
        $this->centrifugoChecker
            ->expects(self::once())
            ->method('assertValidResponseContentType')
            ->with($this->response)
        ;

        $this->response
            ->expects(self::once())
            ->method('getContent')
            ->willReturn(<<<'JSON'
                {
                    "result": {
                        "foo": "bar"
                    }
                }
            JSON
            )
        ;

        $command = $this->createStub(CommandInterface::class);

        $this->commandHistoryLogger
            ->expects(self::once())
            ->method('logCommand')
            ->with($command, true, null)
        ;

        $result = $this->responseProcessor->processResponse($command, $this->response);
        self::assertNull($result);
    }

    public function testInvalidResponse(): void
    {
        $this->response
            ->expects(self::once())
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

    public function testProcessingCentrifugoErrorForSingleCommand(): void
    {
        $this->response
            ->expects(self::once())
            ->method('getContent')
            ->willReturn('{"error":{"message":"test message","code":123}}')
        ;

        $command = $this->createStub(CommandInterface::class);

        $this->commandHistoryLogger
            ->expects(self::once())
            ->method('logCommand')
            ->with($command, false, ['error' => ['message' => 'test message', 'code' => '123']])
        ;

        $this->expectException(CentrifugoErrorException::class);
        $this->expectExceptionCode(123);
        $this->expectExceptionMessage('test message');

        $this->responseProcessor->processResponse($command, $this->response);
    }

    public function testProcessingCentrifugoErrorForBatchRequest(): void
    {
        $this->response
            ->expects(self::once())
            ->method('getContent')
            ->willReturn(<<<'LDJSON'
                {"error":{"message":"test message 2","code":456}}
                null
                {"result":{"channels":["chat","notification"]}}
            LDJSON
            )
        ;

        $this->commandHistoryLogger
            ->expects(self::exactly(3))
            ->method('logCommand')
            ->with(
                ...$this->consecutiveParams(
                    [self::isInstanceOf(PublishCommand::class), false, ['error' => ['message' => 'test message 2', 'code' => 456]]],
                    [self::isInstanceOf(BroadcastCommand::class), true, null],
                    [self::isInstanceOf(ChannelsCommand::class), true, ['channels' => ['chat', 'notification']]],
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
