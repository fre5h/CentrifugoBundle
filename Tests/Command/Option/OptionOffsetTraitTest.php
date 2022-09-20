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

namespace Fresh\CentrifugoBundle\Tests\Command\Option;

use Fresh\CentrifugoBundle\Command\HistoryCommand;
use Fresh\CentrifugoBundle\Service\CentrifugoChecker;
use Fresh\CentrifugoBundle\Service\CentrifugoInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * OptionOffsetTraitTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class OptionOffsetTraitTest extends TestCase
{
    /** @var CentrifugoInterface|MockObject */
    private CentrifugoInterface|MockObject $centrifugo;

    /** @var CentrifugoChecker|MockObject */
    private CentrifugoChecker|MockObject $centrifugoChecker;

    private Command $command;
    private Application $application;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->centrifugo = $this->createMock(CentrifugoInterface::class);
        $this->centrifugoChecker = $this->createMock(CentrifugoChecker::class);
        $command = new HistoryCommand($this->centrifugo, $this->centrifugoChecker);

        $this->application = new Application();
        $this->application->add($command);

        $this->command = $this->application->find('centrifugo:history');
        $this->commandTester = new CommandTester($this->command);
    }

    protected function tearDown(): void
    {
        unset(
            $this->centrifugo,
            $this->centrifugoChecker,
            $this->command,
            $this->application,
            $this->commandTester,
        );
    }

    public function testValidOption(): void
    {
        $this->centrifugo
            ->expects(self::once())
            ->method('history')
        ;

        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'channel' => 'channelName',
                '--offset' => 20,
            ]
        );
    }

    public function testValidOptionShortcut(): void
    {
        $this->centrifugo
            ->expects(self::once())
            ->method('history')
        ;

        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'channel' => 'channelName',
                '-o' => 20,
            ]
        );
    }

    public function testZeroValue(): void
    {
        $this->centrifugo
            ->expects(self::never())
            ->method('history')
        ;

        $this->expectException(InvalidOptionException::class);
        $this->expectExceptionMessage('Option "--offset, -o" should be a valid integer value greater than 0.');

        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'channel' => 'channelName',
                '-o' => 0,
            ]
        );
    }

    public function testNonStringValue(): void
    {
        $this->centrifugo
            ->expects(self::never())
            ->method('history')
        ;

        $this->expectException(InvalidOptionException::class);
        $this->expectExceptionMessage('Option "--offset, -o" should be a valid integer value greater than 0.');

        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'channel' => 'channelName',
                '-o' => 'abcd',
            ]
        );
    }
}
