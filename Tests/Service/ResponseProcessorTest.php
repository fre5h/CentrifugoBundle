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
use Fresh\CentrifugoBundle\Model\CommandInterface;
use Fresh\CentrifugoBundle\Model\ResultableCommandInterface;
use Fresh\CentrifugoBundle\Service\CentrifugoChecker;
use Fresh\CentrifugoBundle\Service\ResponseProcessor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * ResponseProcessorTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class ResponseProcessorTest extends TestCase
{
    /** @var ResponseInterface|MockObject */
    private $response;

    /** @var CentrifugoChecker|MockObject */
    private $centrifugoChecker;

    /** @var ResponseProcessor */
    private $responseProcessor;

    protected function setUp(): void
    {
        $this->response = $this->createMock(ResponseInterface::class);
        $this->centrifugoChecker = $this->createMock(CentrifugoChecker::class);
        $this->responseProcessor = new ResponseProcessor($this->centrifugoChecker);
    }

    protected function tearDown(): void
    {
        unset(
            $this->response,
            $this->centrifugoChecker,
            $this->responseProcessor,
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
            ->willReturn('{"result":{"foo":"bar"}}')
        ;

        $result = $this->responseProcessor->processResponse(
            $this->createStub(ResultableCommandInterface::class),
            $this->response
        );
        self::assertSame(['foo' => "bar"], $result);
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
            ->willReturn('{"result":{"foo":"bar"}}')
        ;

        $result = $this->responseProcessor->processResponse(
            $this->createStub(CommandInterface::class),
            $this->response
        );
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

    public function testProcessingCentrifugoError(): void
    {
        $this->response
            ->expects(self::once())
            ->method('getContent')
            ->willReturn('{"error":{"message":"test message","code":123}}')
        ;

        $this->expectException(CentrifugoErrorException::class);
        $this->expectExceptionCode(123);
        $this->expectExceptionMessage('test message');

        $this->responseProcessor->processResponse(
            $this->createStub(ResultableCommandInterface::class),
            $this->response
        );
    }
}
