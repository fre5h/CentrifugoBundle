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

use Fresh\CentrifugoBundle\Command\RefreshCommand;
use Fresh\CentrifugoBundle\Service\CentrifugoInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * OptionExpireAtTraitTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class OptionExpireAtTraitTest extends TestCase
{
    private CentrifugoInterface&MockObject $centrifugo;
    private Command $command;
    private Application $application;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->centrifugo = $this->createMock(CentrifugoInterface::class);
        $command = new RefreshCommand($this->centrifugo);

        $this->application = new Application();
        $this->application->add($command);

        $this->command = $this->application->find('centrifugo:refresh');
        $this->commandTester = new CommandTester($this->command);
    }

    protected function tearDown(): void
    {
        unset(
            $this->centrifugo,
            $this->command,
            $this->application,
            $this->commandTester,
        );
    }

    #[Test]
    public function validOption(): void
    {
        $this->centrifugo
            ->expects($this->once())
            ->method('refresh')
        ;

        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'user' => 'user123',
                '--expireAt' => 1234567890,
            ],
        );
    }

    #[Test]
    public function zeroValue(): void
    {
        $this->centrifugo
            ->expects($this->never())
            ->method('refresh')
        ;

        $this->expectException(InvalidOptionException::class);
        $this->expectExceptionMessage('Option "--expireAt" should be a valid integer value greater than 0.');

        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'user' => 'user123',
                '--expireAt' => 0,
            ],
        );
    }

    #[Test]
    public function nonStringValue(): void
    {
        $this->centrifugo
            ->expects($this->never())
            ->method('refresh')
        ;

        $this->expectException(InvalidOptionException::class);
        $this->expectExceptionMessage('Option "--expireAt" should be a valid integer value greater than 0.');

        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'user' => 'user123',
                '--expireAt' => 'abcd',
            ],
        );
    }
}
