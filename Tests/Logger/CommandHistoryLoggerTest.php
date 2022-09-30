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

use Fresh\CentrifugoBundle\Logger\CommandHistoryLogger;
use Fresh\CentrifugoBundle\Model\PublishCommand;
use PHPUnit\Framework\TestCase;

/**
 * CommandHistoryLoggerTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class CommandHistoryLoggerTest extends TestCase
{
    private CommandHistoryLogger $commandHistoryLogger;

    protected function setUp(): void
    {
        $this->commandHistoryLogger = new CommandHistoryLogger();
    }

    protected function tearDown(): void
    {
        unset($this->commandHistoryLogger);
    }

    public function testConstructor(): void
    {
        self::assertCount(0, $this->commandHistoryLogger->getCommandHistory());
        self::assertSame(0, $this->commandHistoryLogger->getCommandsCount());
        self::assertSame(0, $this->commandHistoryLogger->getRequestsCount());
        self::assertSame(0, $this->commandHistoryLogger->getSuccessfulCommandsCount());
        self::assertSame(0, $this->commandHistoryLogger->getFailedCommandsCount());
    }

    public function testRequestCount(): void
    {
        self::assertSame(0, $this->commandHistoryLogger->getRequestsCount());
        $this->commandHistoryLogger->increaseRequestsCount();

        self::assertSame(1, $this->commandHistoryLogger->getRequestsCount());
        $this->commandHistoryLogger->increaseRequestsCount();
        self::assertSame(2, $this->commandHistoryLogger->getRequestsCount());

        $this->commandHistoryLogger->clearCommandHistory();
        self::assertSame(0, $this->commandHistoryLogger->getRequestsCount());
    }

    public function testFullFlow(): void
    {
        $command = new PublishCommand([], 'channelA');
        $this->commandHistoryLogger->logCommand($command, true, ['test']);
        self::assertCount(1, $this->commandHistoryLogger->getCommandHistory());
        self::assertSame(
            [
                'command' => $command,
                'result' => ['test'],
                'success' => true,
            ],
            $this->commandHistoryLogger->getCommandHistory()[0]
        );
        self::assertSame(1, $this->commandHistoryLogger->getCommandsCount());
        self::assertSame(1, $this->commandHistoryLogger->getSuccessfulCommandsCount());
        self::assertSame(0, $this->commandHistoryLogger->getFailedCommandsCount());

        $this->commandHistoryLogger->logCommand(new PublishCommand([], 'channelB'), false, []);
        self::assertCount(2, $this->commandHistoryLogger->getCommandHistory());
        self::assertSame(2, $this->commandHistoryLogger->getCommandsCount());
        self::assertSame(1, $this->commandHistoryLogger->getSuccessfulCommandsCount());
        self::assertSame(1, $this->commandHistoryLogger->getFailedCommandsCount());

        $this->commandHistoryLogger->clearCommandHistory();
        self::assertCount(0, $this->commandHistoryLogger->getCommandHistory());
        self::assertSame(0, $this->commandHistoryLogger->getCommandsCount());
        self::assertSame(0, $this->commandHistoryLogger->getSuccessfulCommandsCount());
        self::assertSame(0, $this->commandHistoryLogger->getFailedCommandsCount());
    }
}
