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
use Fresh\CentrifugoBundle\Model\Disconnect;
use Fresh\CentrifugoBundle\Service\CentrifugoInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * DisconnectCommandTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class DisconnectCommandTest extends TestCase
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

    #[Test]
    public function successfulExecutionWithRequiredParameters(): void
    {
        $this->centrifugo
            ->expects($this->once())
            ->method('disconnect')
            ->with('user123')
        ;

        $result = $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'user' => 'user123',
            ],
        );
        $this->assertSame(0, $result);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('DONE', $output);
    }

    #[Test]
    public function successfulExecutionWithAllParameters(): void
    {
        $this->centrifugo
            ->expects($this->once())
            ->method('disconnect')
            ->with('user123', ['clientID1'], 'clientID2', 'sessionID', $this->isInstanceOf(Disconnect::class))
        ;

        $result = $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'user' => 'user123',
                '--whitelist' => 'clientID1',
                '--client' => 'clientID2',
                '--session' => 'sessionID',
                '--disconnectCode' => 999,
                '--disconnectReason' => 'some reason',
            ],
        );
        $this->assertSame(0, $result);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('DONE', $output);
    }

    #[Test]
    public function exceptionForMissingDisconnectCode(): void
    {
        $this->centrifugo
            ->expects($this->never())
            ->method('disconnect')
        ;

        $this->expectException(InvalidOptionException::class);
        $this->expectExceptionMessage('Options "--disconnectReason" and "--disconnectCode" should set be together.');

        $result = $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'user' => 'user123',
                '--whitelist' => 'clientID1',
                '--client' => 'clientID2',
                '--session' => 'sessionID',
                '--disconnectReason' => 'some reason',
            ],
        );
        $this->assertSame(0, $result);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('DONE', $output);
    }

    #[Test]
    public function exceptionForMissingDisconnectReason(): void
    {
        $this->centrifugo
            ->expects($this->never())
            ->method('disconnect')
        ;

        $this->expectException(InvalidOptionException::class);
        $this->expectExceptionMessage('Options "--disconnectReason" and "--disconnectCode" should set be together.');

        $result = $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'user' => 'user123',
                '--whitelist' => 'clientID1',
                '--client' => 'clientID2',
                '--session' => 'sessionID',
                '--disconnectCode' => 999,
            ],
        );
        $this->assertSame(0, $result);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('DONE', $output);
    }

    #[Test]
    public function exception(): void
    {
        $this->centrifugo
            ->expects($this->once())
            ->method('disconnect')
            ->willThrowException(new \Exception('test'))
        ;

        $result = $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'user' => 'user123',
            ],
        );
        $this->assertSame(1, $result);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('test', $output);
    }
}
