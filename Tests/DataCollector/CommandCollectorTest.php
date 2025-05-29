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

namespace Fresh\CentrifugoBundle\Tests\Logger;

use Fresh\CentrifugoBundle\DataCollector\CentrifugoCollector;
use Fresh\CentrifugoBundle\Logger\CommandHistoryLogger;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CommandCollectorTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class CommandCollectorTest extends TestCase
{
    private CommandHistoryLogger&MockObject $commandHistoryLogger;
    private CentrifugoCollector $centrifugoCollector;

    protected function setUp(): void
    {
        $this->commandHistoryLogger = $this->createMock(CommandHistoryLogger::class);
        $this->centrifugoCollector = new CentrifugoCollector($this->commandHistoryLogger);
    }

    protected function tearDown(): void
    {
        unset(
            $this->commandHistoryLogger,
            $this->centrifugoCollector,
        );
    }

    #[Test]
    public function constructor(): void
    {
        $this->assertEquals(0, $this->centrifugoCollector->getCommandsCount());
        $this->assertEquals(0, $this->centrifugoCollector->getRequestsCount());
        $this->assertEquals(0, $this->centrifugoCollector->getSuccessfulCommandsCount());
        $this->assertEquals(0, $this->centrifugoCollector->getFailedCommandsCount());
        $this->assertCount(0, $this->centrifugoCollector->getCommandHistory());
        $this->assertSame('centrifugo', $this->centrifugoCollector->getName());
    }

    #[Test]
    public function collectAndReset(): void
    {
        $this->commandHistoryLogger
            ->expects($this->exactly(2))
            ->method('getCommandHistory')
            ->willReturn([['test']], [['test'], ['test']])
        ;
        $this->commandHistoryLogger
            ->expects($this->exactly(2))
            ->method('getCommandsCount')
            ->willReturnOnConsecutiveCalls(1, 2)
        ;
        $this->commandHistoryLogger
            ->expects($this->exactly(2))
            ->method('getRequestsCount')
            ->willReturnOnConsecutiveCalls(1, 2)
        ;
        $this->commandHistoryLogger
            ->expects($this->exactly(2))
            ->method('getSuccessfulCommandsCount')
            ->willReturnOnConsecutiveCalls(1, 1)
        ;
        $this->commandHistoryLogger
            ->expects($this->exactly(2))
            ->method('getFailedCommandsCount')
            ->willReturnOnConsecutiveCalls(0, 1)
        ;

        $this->centrifugoCollector->collect(
            $this->createStub(Request::class),
            $this->createStub(Response::class)
        );

        $this->assertEquals(1, $this->centrifugoCollector->getCommandsCount());
        $this->assertEquals(1, $this->centrifugoCollector->getRequestsCount());
        $this->assertEquals(1, $this->centrifugoCollector->getSuccessfulCommandsCount());
        $this->assertEquals(0, $this->centrifugoCollector->getFailedCommandsCount());
        $this->assertCount(1, $this->centrifugoCollector->getCommandHistory());

        $this->centrifugoCollector->collect(
            $this->createStub(Request::class),
            $this->createStub(Response::class),
        );

        $this->assertEquals(2, $this->centrifugoCollector->getCommandsCount());
        $this->assertEquals(2, $this->centrifugoCollector->getRequestsCount());
        $this->assertEquals(1, $this->centrifugoCollector->getSuccessfulCommandsCount());
        $this->assertEquals(1, $this->centrifugoCollector->getFailedCommandsCount());
        $this->assertCount(2, $this->centrifugoCollector->getCommandHistory());

        $this->centrifugoCollector->reset();

        $this->assertEquals(0, $this->centrifugoCollector->getCommandsCount());
        $this->assertEquals(0, $this->centrifugoCollector->getRequestsCount());
        $this->assertEquals(0, $this->centrifugoCollector->getSuccessfulCommandsCount());
        $this->assertEquals(0, $this->centrifugoCollector->getFailedCommandsCount());
        $this->assertCount(0, $this->centrifugoCollector->getCommandHistory());
    }
}
