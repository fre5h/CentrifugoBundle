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
use Fresh\CentrifugoBundle\Exception\InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * InvalidArgumentExceptionTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class InvalidArgumentExceptionTest extends TestCase
{
    private InvalidArgumentException $exception;

    protected function setUp(): void
    {
        $this->exception = new InvalidArgumentException();
    }

    protected function tearDown(): void
    {
        unset($this->exception);
    }

    #[Test]
    public function exception(): void
    {
        self::assertInstanceOf(ExceptionInterface::class, $this->exception);
        self::assertInstanceOf(\InvalidArgumentException::class, $this->exception);
    }
}
