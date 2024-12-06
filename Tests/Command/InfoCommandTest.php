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

use Fresh\CentrifugoBundle\Command\InfoCommand;
use Fresh\CentrifugoBundle\Service\CentrifugoInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * InfoCommandTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class InfoCommandTest extends TestCase
{
    private CentrifugoInterface&MockObject $centrifugo;
    private Command $command;
    private Application $application;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->centrifugo = $this->createMock(CentrifugoInterface::class);
        $command = new InfoCommand($this->centrifugo);

        $this->application = new Application();
        $this->application->add($command);

        $this->command = $this->application->find('centrifugo:info');
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
    public function successfulExecution(): void
    {
        $this->centrifugo
            ->expects($this->once())
            ->method('info')
            ->willReturn(
                [
                    'nodes' => [
                        [
                            'name' => 'Test',
                            'metrics' => [
                                'interval' => 60,
                                'items' => [
                                    'process.virtual.memory_max_bytes' => -1,
                                ],
                            ],
                        ],
                    ],
                ],
            )
        ;

        $result = $this->commandTester->execute(['command' => $this->command->getName()]);
        $this->assertSame(0, $result);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Info', $output);
        $this->assertStringContainsString('Node Test', $output);
        $this->assertStringContainsString('metrics', $output);
        $this->assertStringContainsString('  ├ interval: 60', $output);
        $this->assertStringContainsString('  └ items', $output);
        $this->assertStringContainsString('    └ process.virtual.memory_max_bytes: -1', $output);
    }

    #[Test]
    public function noData(): void
    {
        $this->centrifugo
            ->expects($this->once())
            ->method('info')
            ->willReturn(['nodes' => []])
        ;

        $result = $this->commandTester->execute(['command' => $this->command->getName()]);
        $this->assertSame(0, $result);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('NO DATA', $output);
    }

    #[Test]
    public function exception(): void
    {
        $this->centrifugo
            ->expects($this->once())
            ->method('info')
            ->willThrowException(new \Exception('test'))
        ;

        $result = $this->commandTester->execute(['command' => $this->command->getName()]);
        $this->assertSame(1, $result);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('test', $output);
    }

    #[Test]
    public function unexpectedValueException(): void
    {
        $this->centrifugo
            ->expects($this->once())
            ->method('info')
            ->willReturn(
                [
                    'nodes' => [
                        [
                            'name' => 'Test',
                            'bar' => new \stdClass(),
                        ],
                    ],
                ],
            )
        ;

        $result = $this->commandTester->execute(['command' => $this->command->getName()]);
        $this->assertSame(1, $result);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Value is not an array, nor a string', $output);
    }
}
