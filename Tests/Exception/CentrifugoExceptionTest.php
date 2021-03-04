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
use PHPUnit\Framework\TestCase;

/**
 * CentrifugoExceptionTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class CentrifugoExceptionTest extends TestCase
{
    private CentrifugoException $exception;

    protected function setUp(): void
    {
        $this->exception = new CentrifugoException();
    }

    protected function tearDown(): void
    {
        unset($this->exception);
    }

    public function testException(): void
    {
        self::assertInstanceOf(\Exception::class, $this->exception);
        self::assertInstanceOf(ExceptionInterface::class, $this->exception);
    }
}
