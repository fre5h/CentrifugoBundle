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
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * CentrifugoCheckerTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class CentrifugoCheckerTest extends TestCase
{
    /** @var ResponseInterface */
    private $response;

    /** @var CentrifugoChecker */
    private $centrifugoChecker;

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

    public function testInvalidChannelName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid channel name. Only ASCII symbols must be used in channel string.');

        $this->centrifugoChecker->assertValidChannelName('Hallöchen');
    }

    public function testInvalidChannelNameLength(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid channel name length. Maximum allowed length is 10.');

        $this->centrifugoChecker->assertValidChannelName('ABCDEFGHIJK');
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testValidChannelName(): void
    {
        $this->centrifugoChecker->assertValidChannelName('1234567890');
    }

    public function testInvalidResponseStatusCode(): void
    {
        $this->response
            ->expects(self::once())
            ->method('getStatusCode')
            ->willReturn(500)
        ;

        $this->expectException(CentrifugoException::class);
        $this->expectExceptionMessage('Wrong status code for Centrifugo response');

        $this->centrifugoChecker->assertValidResponseStatusCode($this->response);
    }

    public function testValidResponseStatusCode(): void
    {
        $this->response
            ->expects(self::once())
            ->method('getStatusCode')
            ->willReturn(200)
        ;

        $this->centrifugoChecker->assertValidResponseStatusCode($this->response);
    }

    public function testInvalidResponseHeaders(): void
    {
        $this->response
            ->expects(self::once())
            ->method('getHeaders')
            ->with(false)
            ->willReturn([])
        ;

        $this->expectException(CentrifugoException::class);
        $this->expectExceptionMessage('Missing "content-type" header in Centrifugo response');

        $this->centrifugoChecker->assertValidResponseHeaders($this->response);
    }

    public function testValidResponseHeaders(): void
    {
        $this->response
            ->expects(self::once())
            ->method('getHeaders')
            ->with(false)
            ->willReturn(['content-type' => []])
        ;

        $this->centrifugoChecker->assertValidResponseHeaders($this->response);
    }

    public function testInvalidResponseContentType(): void
    {
        $this->response
            ->expects(self::once())
            ->method('getHeaders')
            ->with(false)
            ->willReturn(['content-type' => ['text/html']])
        ;

        $this->expectException(CentrifugoException::class);
        $this->expectExceptionMessage('Unexpected content type for Centrifugo response');

        $this->centrifugoChecker->assertValidResponseContentType($this->response);
    }

    public function testValidResponseContentType(): void
    {
        $this->response
            ->expects(self::once())
            ->method('getHeaders')
            ->with(false)
            ->willReturn(['content-type' => ['application/json']])
        ;

        $this->centrifugoChecker->assertValidResponseContentType($this->response);
    }
}
