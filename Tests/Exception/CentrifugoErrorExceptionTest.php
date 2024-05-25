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

use Fresh\CentrifugoBundle\Exception\CentrifugoErrorException;
use Fresh\CentrifugoBundle\Exception\CentrifugoException;
use Fresh\CentrifugoBundle\Exception\ExceptionInterface;
use Fresh\CentrifugoBundle\Model\CommandInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * CentrifugoErrorExceptionTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class CentrifugoErrorExceptionTest extends TestCase
{
    private CentrifugoErrorException $exception;
    private CommandInterface|MockObject $command;
    private ResponseInterface|MockObject $response;

    protected function setUp(): void
    {
        $this->command = $this->createStub(CommandInterface::class);
        $this->response = $this->createStub(ResponseInterface::class);

        $this->exception = new CentrifugoErrorException($this->command, $this->response);
    }

    protected function tearDown(): void
    {
        unset($this->exception);
    }

    #[Test]
    public function getCommand(): void
    {
        self::assertSame($this->command, $this->exception->getCommand());
    }

    #[Test]
    public function getResponse(): void
    {
        self::assertSame($this->response, $this->exception->getResponse());
    }

    #[Test]
    public function exception(): void
    {
        self::assertInstanceOf(CentrifugoException::class, $this->exception);
        self::assertInstanceOf(ExceptionInterface::class, $this->exception);
    }
}
