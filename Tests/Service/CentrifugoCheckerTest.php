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

use Fresh\CentrifugoBundle\Exception\CentrifugoException;
use Fresh\CentrifugoBundle\Exception\InvalidArgumentException;
use Fresh\CentrifugoBundle\Service\CentrifugoChecker;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * CentrifugoCheckerTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class CentrifugoCheckerTest extends TestCase
{
    private ResponseInterface|MockObject $response;
    private CentrifugoChecker $centrifugoChecker;

    protected function setUp(): void
    {
        $this->response = $this->createMock(ResponseInterface::class);
        $this->centrifugoChecker = new CentrifugoChecker(10);
    }

    protected function tearDown(): void
    {
        unset(
            $this->response,
            $this->centrifugoChecker,
        );
    }

    #[Test]
    public function invalidChannelName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid channel name. Only ASCII symbols must be used in channel string.');

        $this->centrifugoChecker->assertValidChannelName('Hallöchen');
    }

    #[Test]
    public function invalidChannelNameLength(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid channel name length. Maximum allowed length is 10.');

        $this->centrifugoChecker->assertValidChannelName('ABCDEFGHIJK');
    }

    #[Test]
    public function validChannelName(): void
    {
        $this->assertTrue($this->centrifugoChecker->assertValidChannelName('1234567890'));
    }

    #[Test]
    public function invalidResponseStatusCode(): void
    {
        $this->response
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(500)
        ;

        $this->expectException(CentrifugoException::class);
        $this->expectExceptionMessage('Wrong status code for Centrifugo response');

        $this->centrifugoChecker->assertValidResponseStatusCode($this->response);
    }

    #[Test]
    public function validResponseStatusCode(): void
    {
        $this->response
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200)
        ;

        $this->centrifugoChecker->assertValidResponseStatusCode($this->response);
    }

    #[Test]
    public function invalidResponseHeaders(): void
    {
        $this->response
            ->expects($this->once())
            ->method('getHeaders')
            ->with(false)
            ->willReturn([])
        ;

        $this->expectException(CentrifugoException::class);
        $this->expectExceptionMessage('Missing "content-type" header in Centrifugo response');

        $this->centrifugoChecker->assertValidResponseHeaders($this->response);
    }

    #[Test]
    public function validResponseHeaders(): void
    {
        $this->response
            ->expects($this->once())
            ->method('getHeaders')
            ->with(false)
            ->willReturn(['content-type' => []])
        ;

        $this->centrifugoChecker->assertValidResponseHeaders($this->response);
    }

    #[Test]
    public function invalidResponseContentType(): void
    {
        $this->response
            ->expects($this->once())
            ->method('getHeaders')
            ->with(false)
            ->willReturn(['content-type' => ['text/html']])
        ;

        $this->expectException(CentrifugoException::class);
        $this->expectExceptionMessage('Unexpected content type for Centrifugo response');

        $this->centrifugoChecker->assertValidResponseContentType($this->response);
    }

    #[Test]
    public function validResponseContentType(): void
    {
        $this->response
            ->expects($this->once())
            ->method('getHeaders')
            ->with(false)
            ->willReturn(['content-type' => ['application/json']])
        ;

        $this->centrifugoChecker->assertValidResponseContentType($this->response);
    }
}
