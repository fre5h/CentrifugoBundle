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
use Fresh\CentrifugoBundle\Exception\UnexpectedValueException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * UnexpectedValueExceptionTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class UnexpectedValueExceptionTest extends TestCase
{
    private UnexpectedValueException $exception;

    protected function setUp(): void
    {
        $this->exception = new UnexpectedValueException();
    }

    protected function tearDown(): void
    {
        unset($this->exception);
    }

    #[Test]
    public function exception(): void
    {
        $this->assertInstanceOf(ExceptionInterface::class, $this->exception);
        $this->assertInstanceOf(\UnexpectedValueException::class, $this->exception);
    }
}
