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
use Fresh\CentrifugoBundle\Model\BatchRequest;
use Fresh\CentrifugoBundle\Model\PublishCommand;
use PHPUnit\Framework\TestCase;

/**
 * CommandHistoryLoggerTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class CommandHistoryLoggerTest extends TestCase
{
    /** @var CommandHistoryLogger */
    private $commandHistoryLogger;

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
    }

    public function testAllFlow(): void
    {
        self::assertCount(0, $this->commandHistoryLogger->getCommandHistory());
        $this->commandHistoryLogger->logCommand(new PublishCommand([], 'channelA'));
        self::assertCount(1, $this->commandHistoryLogger->getCommandHistory());
        $this->commandHistoryLogger->logCommand(new BatchRequest([new PublishCommand([], 'channelB'), new PublishCommand([], 'channelC')]));
        self::assertCount(3, $this->commandHistoryLogger->getCommandHistory());
        $this->commandHistoryLogger->clearCommandHistory();
        self::assertCount(0, $this->commandHistoryLogger->getCommandHistory());
    }
}
