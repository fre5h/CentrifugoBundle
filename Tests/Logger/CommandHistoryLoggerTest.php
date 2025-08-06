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
use PHPUnit\Framework\Attributes\Test;
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

    #[Test]
    public function constructor(): void
    {
        $this->assertCount(0, $this->commandHistoryLogger->getCommandHistory());
        $this->assertSame(0, $this->commandHistoryLogger->getCommandsCount());
        $this->assertSame(0, $this->commandHistoryLogger->getRequestsCount());
        $this->assertSame(0, $this->commandHistoryLogger->getSuccessfulCommandsCount());
        $this->assertSame(0, $this->commandHistoryLogger->getFailedCommandsCount());
    }

    #[Test]
    public function requestCount(): void
    {
        $this->assertSame(0, $this->commandHistoryLogger->getRequestsCount());
        $this->commandHistoryLogger->increaseRequestsCount();

        $this->assertSame(1, $this->commandHistoryLogger->getRequestsCount());
        $this->commandHistoryLogger->increaseRequestsCount();
        $this->assertSame(2, $this->commandHistoryLogger->getRequestsCount());

        $this->commandHistoryLogger->clearCommandHistory();
        $this->assertSame(0, $this->commandHistoryLogger->getRequestsCount());
    }

    #[Test]
    public function fullFlow(): void
    {
        $command = new PublishCommand([], 'channelA');
        $this->commandHistoryLogger->logCommand($command, true, ['test']);
        $this->assertCount(1, $this->commandHistoryLogger->getCommandHistory());
        $this->assertSame(
            [
                'command' => $command,
                'result' => ['test'],
                'success' => true,
            ],
            $this->commandHistoryLogger->getCommandHistory()[0],
        );
        $this->assertSame(1, $this->commandHistoryLogger->getCommandsCount());
        $this->assertSame(1, $this->commandHistoryLogger->getSuccessfulCommandsCount());
        $this->assertSame(0, $this->commandHistoryLogger->getFailedCommandsCount());

        $this->commandHistoryLogger->logCommand(new PublishCommand([], 'channelB'), false, []);
        $this->assertCount(2, $this->commandHistoryLogger->getCommandHistory());
        $this->assertSame(2, $this->commandHistoryLogger->getCommandsCount());
        $this->assertSame(1, $this->commandHistoryLogger->getSuccessfulCommandsCount());
        $this->assertSame(1, $this->commandHistoryLogger->getFailedCommandsCount());

        $this->commandHistoryLogger->clearCommandHistory();
        $this->assertCount(0, $this->commandHistoryLogger->getCommandHistory());
        $this->assertSame(0, $this->commandHistoryLogger->getCommandsCount());
        $this->assertSame(0, $this->commandHistoryLogger->getSuccessfulCommandsCount());
        $this->assertSame(0, $this->commandHistoryLogger->getFailedCommandsCount());
    }
}
