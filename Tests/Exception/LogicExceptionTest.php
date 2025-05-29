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

use Fresh\CentrifugoBundle\Exception\ExceptionInterface;
use Fresh\CentrifugoBundle\Exception\LogicException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * LogicExceptionTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class LogicExceptionTest extends TestCase
{
    private LogicException $exception;

    protected function setUp(): void
    {
        $this->exception = new LogicException();
    }

    protected function tearDown(): void
    {
        unset($this->exception);
    }

    #[Test]
    public function exception(): void
    {
        $this->assertInstanceOf(ExceptionInterface::class, $this->exception);
        $this->assertInstanceOf(\LogicException::class, $this->exception);
    }
}
