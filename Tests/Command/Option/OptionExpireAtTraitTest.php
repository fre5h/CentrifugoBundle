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
    /** @var CentrifugoInterface|MockObject */
    private CentrifugoInterface|MockObject $centrifugo;

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

    public function testValidOption(): void
    {
        $this->centrifugo
            ->expects(self::once())
            ->method('refresh')
        ;

        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'user' => 'user123',
                '--expireAt' => 1234567890,
            ]
        );
    }

    public function testZeroValue(): void
    {
        $this->centrifugo
            ->expects(self::never())
            ->method('refresh')
        ;

        $this->expectException(InvalidOptionException::class);
        $this->expectExceptionMessage('Option "--expireAt" should be a valid integer value greater than 0.');

        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'user' => 'user123',
                '--expireAt' => 0,
            ]
        );
    }

    public function testNonStringValue(): void
    {
        $this->centrifugo
            ->expects(self::never())
            ->method('refresh')
        ;

        $this->expectException(InvalidOptionException::class);
        $this->expectExceptionMessage('Option "--expireAt" should be a valid integer value greater than 0.');

        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'user' => 'user123',
                '--expireAt' => 'abcd',
            ]
        );
    }
}
