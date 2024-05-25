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
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * OptionLimitTraitTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class OptionLimitTraitTest extends TestCase
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

    #[Test]
    public function validOption(): void
    {
        $this->centrifugo
            ->expects(self::once())
            ->method('history')
        ;

        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'channel' => 'channelName',
                '--limit' => 20,
            ]
        );
    }

    #[Test]
    public function zeroValue(): void
    {
        $this->centrifugo
            ->expects(self::never())
            ->method('history')
        ;

        $this->expectException(InvalidOptionException::class);
        $this->expectExceptionMessage('Option "--limit" should be a valid integer value greater than 0.');

        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'channel' => 'channelName',
                '--limit' => 0,
            ]
        );
    }

    #[Test]
    public function nonStringValue(): void
    {
        $this->centrifugo
            ->expects(self::never())
            ->method('history')
        ;

        $this->expectException(InvalidOptionException::class);
        $this->expectExceptionMessage('Option "--limit" should be a valid integer value greater than 0.');

        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'channel' => 'channelName',
                '--limit' => 'abcd',
            ]
        );
    }
}
