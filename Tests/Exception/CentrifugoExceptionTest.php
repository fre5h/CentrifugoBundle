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

namespace Fresh\CentrifugoBundle\Tests\Exception;

use Fresh\CentrifugoBundle\Exception\CentrifugoException;
use Fresh\CentrifugoBundle\Exception\ExceptionInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * CentrifugoExceptionTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class CentrifugoExceptionTest extends TestCase
{
    private CentrifugoException $exception;

    private ResponseInterface|MockObject $response;

    protected function setUp(): void
    {
        $this->response = $this->createStub(ResponseInterface::class);

        $this->exception = new CentrifugoException($this->response);
    }

    protected function tearDown(): void
    {
        unset($this->exception);
    }

    #[Test]
    public function getResponse(): void
    {
        self::assertSame($this->response, $this->exception->getResponse());
    }

    #[Test]
    public function exception(): void
    {
        self::assertInstanceOf(\Exception::class, $this->exception);
        self::assertInstanceOf(ExceptionInterface::class, $this->exception);
    }
}
