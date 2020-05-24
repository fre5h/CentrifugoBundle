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
use Fresh\CentrifugoBundle\Service\Centrifugo;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class ChannelsCommandTest extends TestCase
{
    /** @var Centrifugo|MockObject */
    private $centrifugo;

    /** @var Command */
    private $command;

    /** @var Application */
    private $application;

    /** @var CommandTester */
    private $commandTester;

    protected function setUp(): void
    {
        $this->centrifugo = $this->createMock(Centrifugo::class);
        $command = new ChannelsCommand($this->centrifugo);

        $this->application = new Application();
        $this->application->add($command);

        $this->command = $this->application->find('centrifugo:channel');
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

    public function testSuccessfulExecute(): void
    {
        $this->centrifugo
            ->expects(self::once())
            ->method('channels')
            ->willReturn(['channels' => ['channelA', 'channelB']])
        ;

        $result = $this->commandTester->execute(['command' => $this->command->getName()]);
        self::assertSame(0, $result);

        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString('Channels', $output);
        self::assertStringContainsString('* channelA', $output);
        self::assertStringContainsString('* channelB', $output);
        self::assertStringContainsString('TOTAL: 2', $output);
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
            ->willThrowException(new \Exception('test', 5))
        ;

        $result = $this->commandTester->execute(['command' => $this->command->getName()]);
        self::assertSame(5, $result);

        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString('test', $output);
    }
}
