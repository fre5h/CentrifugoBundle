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
use PHPUnit\Framework\TestCase;

/**
 * LogicExceptionTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class LogicExceptionTest extends TestCase
{
    /** @var LogicException */
    private $exception;

    protected function setUp(): void
    {
        $this->exception = new LogicException();
    }

    protected function tearDown(): void
    {
        unset($this->exception);
    }

    public function testException(): void
    {
        self::assertInstanceOf(ExceptionInterface::class, $this->exception);
        self::assertInstanceOf(\LogicException::class, $this->exception);
    }
}
