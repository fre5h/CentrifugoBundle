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

namespace Fresh\CentrifugoBundle\Tests\Command\Argument;

use Fresh\CentrifugoBundle\Command\DisconnectCommand;
use Fresh\CentrifugoBundle\Service\CentrifugoInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * ArgumentUserTraitTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class ArgumentUserTraitTest extends TestCase
{
    private CentrifugoInterface&MockObject $centrifugo;
    private Command $command;
    private Application $application;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->centrifugo = $this->createMock(CentrifugoInterface::class);
        $command = new DisconnectCommand($this->centrifugo);

        $this->application = new Application();
        $this->application->addCommand($command);

        $this->command = $this->application->find('centrifugo:disconnect');
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
    public function invalidUser(): void
    {
        $this->centrifugo
            ->expects($this->never())
            ->method('disconnect')
        ;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument "user" is not a string.');

        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'user' => ['user123'],
            ]
        );
    }
}
