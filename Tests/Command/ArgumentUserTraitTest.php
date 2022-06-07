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

namespace Fresh\CentrifugoBundle\Tests\Command;

use Fresh\CentrifugoBundle\Command\DisconnectCommand;
use Fresh\CentrifugoBundle\Service\CentrifugoInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Tester\CommandTester;

final class ArgumentUserTraitTest extends TestCase
{
    /** @var CentrifugoInterface|MockObject */
    private CentrifugoInterface|MockObject $centrifugo;

    private Command $command;
    private Application $application;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->centrifugo = $this->createMock(CentrifugoInterface::class);
        $command = new DisconnectCommand($this->centrifugo);

        $this->application = new Application();
        $this->application->add($command);

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

    public function testInvalidUser(): void
    {
        $this->centrifugo
            ->expects(self::never())
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
