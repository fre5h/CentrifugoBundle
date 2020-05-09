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
    /** @var CommandHistoryLogger */
    private $commandHistoryLogger;

    /** @var CentrifugoCollector */
    private $centrifugoCollector;

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

    public function testConstructor(): void
    {
        self::assertEquals(0, $this->centrifugoCollector->getCommandsCount());
        self::assertEquals(0, $this->centrifugoCollector->getRequestsCount());
        self::assertEquals(0, $this->centrifugoCollector->getSuccessfulCommandsCount());
        self::assertEquals(0, $this->centrifugoCollector->getFailedCommandsCount());
        self::assertCount(0, $this->centrifugoCollector->getCommandHistory());
        self::assertSame('centrifugo', $this->centrifugoCollector->getName());
    }

    public function testCollectAndReset(): void
    {
        $this->commandHistoryLogger
            ->expects(self::exactly(2))
            ->method('getCommandHistory')
            ->willReturn([['test']], [['test'], ['test']])
        ;
        $this->commandHistoryLogger
            ->expects(self::exactly(2))
            ->method('getCommandsCount')
            ->willReturnOnConsecutiveCalls(1, 2)
        ;
        $this->commandHistoryLogger
            ->expects(self::exactly(2))
            ->method('getRequestsCount')
            ->willReturnOnConsecutiveCalls(1, 2)
        ;
        $this->commandHistoryLogger
            ->expects(self::exactly(2))
            ->method('getSuccessfulCommandsCount')
            ->willReturnOnConsecutiveCalls(1, 1)
        ;
        $this->commandHistoryLogger
            ->expects(self::exactly(2))
            ->method('getFailedCommandsCount')
            ->willReturnOnConsecutiveCalls(0, 1)
        ;


        $this->centrifugoCollector->collect(
            $this->createStub(Request::class),
            $this->createStub(Response::class),
            null
        );

        self::assertEquals(1, $this->centrifugoCollector->getCommandsCount());
        self::assertEquals(1, $this->centrifugoCollector->getRequestsCount());
        self::assertEquals(1, $this->centrifugoCollector->getSuccessfulCommandsCount());
        self::assertEquals(0, $this->centrifugoCollector->getFailedCommandsCount());
        self::assertCount(1, $this->centrifugoCollector->getCommandHistory());

        $this->centrifugoCollector->collect(
            $this->createStub(Request::class),
            $this->createStub(Response::class),
            null
        );

        self::assertEquals(2, $this->centrifugoCollector->getCommandsCount());
        self::assertEquals(2, $this->centrifugoCollector->getRequestsCount());
        self::assertEquals(1, $this->centrifugoCollector->getSuccessfulCommandsCount());
        self::assertEquals(1, $this->centrifugoCollector->getFailedCommandsCount());
        self::assertCount(2, $this->centrifugoCollector->getCommandHistory());

        $this->centrifugoCollector->reset();

        self::assertEquals(0, $this->centrifugoCollector->getCommandsCount());
        self::assertEquals(0, $this->centrifugoCollector->getRequestsCount());
        self::assertEquals(0, $this->centrifugoCollector->getSuccessfulCommandsCount());
        self::assertEquals(0, $this->centrifugoCollector->getFailedCommandsCount());
        self::assertCount(0, $this->centrifugoCollector->getCommandHistory());
    }
}
