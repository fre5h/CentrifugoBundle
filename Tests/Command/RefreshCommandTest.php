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

use Fresh\CentrifugoBundle\Command\RefreshCommand;
use Fresh\CentrifugoBundle\Service\CentrifugoInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * RefreshCommandTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class RefreshCommandTest extends TestCase
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

    public function testSuccessfulExecutionWithRequiredParameters(): void
    {
        $this->centrifugo
            ->expects(self::once())
            ->method('refresh')
            ->with('user123')
        ;

        $result = $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'user' => 'user123',
            ]
        );
        self::assertSame(0, $result);

        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString('DONE', $output);
    }

    public function testSuccessfulExecutionWithAllParameters(): void
    {
        $this->centrifugo
            ->expects(self::once())
            ->method('refresh')
            ->with('user123', 'clientID', 'sessionID', true, 1234567890)
        ;

        $result = $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'user' => 'user123',
                '--client' => 'clientID',
                '--session' => 'sessionID',
                '--expired' => true,
                '--expireAt' => 1234567890,
            ]
        );
        self::assertSame(0, $result);

        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString('DONE', $output);
    }

    public function testException(): void
    {
        $this->centrifugo
            ->expects(self::once())
            ->method('refresh')
            ->willThrowException(new \Exception('test'))
        ;

        $result = $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'user' => 'user123',
            ]
        );
        self::assertSame(1, $result);

        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString('test', $output);
    }
}
