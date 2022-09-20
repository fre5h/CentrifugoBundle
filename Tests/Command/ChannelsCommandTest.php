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

use Fresh\CentrifugoBundle\Command\ChannelsCommand;
use Fresh\CentrifugoBundle\Service\CentrifugoInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * ChannelsCommandTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class ChannelsCommandTest extends TestCase
{
    /** @var CentrifugoInterface|MockObject */
    private CentrifugoInterface|MockObject $centrifugo;

    private Command $command;
    private Application $application;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->centrifugo = $this->createMock(CentrifugoInterface::class);
        $command = new ChannelsCommand($this->centrifugo);

        $this->application = new Application();
        $this->application->add($command);

        $this->command = $this->application->find('centrifugo:channels');
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

    public function testSuccessfulExecutionWithoutPattern(): void
    {
        $this->centrifugo
            ->expects(self::once())
            ->method('channels')
            ->willReturn(['channels' => ['channelA' => ['num_clients' => 33], 'channelB' => ['num_clients' => 25]]])
        ;

        $result = $this->commandTester->execute(['command' => $this->command->getName()]);
        self::assertSame(0, $result);

        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString('channelA       33', $output);
        self::assertStringContainsString('channelB       25', $output);
        self::assertStringContainsString('Total Channels: 2', $output);
    }

    public function testSuccessfulExecutionWithPattern(): void
    {
        $this->centrifugo
            ->expects(self::once())
            ->method('channels')
            ->willReturn(['channels' => ['channelA' => ['num_clients' => 33]]])
        ;

        $result = $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'pattern' => 'channelA'
            ]
        );
        self::assertSame(0, $result);

        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString('channelA       33', $output);
        self::assertStringContainsString('Total Channels: 1', $output);
    }

    public function testNoData(): void
    {
        $this->centrifugo
            ->expects(self::once())
            ->method('channels')
            ->willReturn(['channels' => []])
        ;

        $result = $this->commandTester->execute(['command' => $this->command->getName()]);
        self::assertSame(0, $result);

        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString('NO DATA', $output);
    }

    public function testException(): void
    {
        $this->centrifugo
            ->expects(self::once())
            ->method('channels')
            ->willThrowException(new \Exception('test'))
        ;

        $result = $this->commandTester->execute(['command' => $this->command->getName()]);
        self::assertSame(1, $result);

        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString('test', $output);
    }
}
