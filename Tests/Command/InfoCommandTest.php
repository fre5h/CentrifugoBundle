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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class InfoCommandTest extends TestCase
{
    /** @var CentrifugoInterface|MockObject */
    private $centrifugo;

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

    public function testSuccessfulExecution(): void
    {
        $this->centrifugo
            ->expects(self::once())
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
                ]
            )
        ;

        $result = $this->commandTester->execute(['command' => $this->command->getName()]);
        self::assertSame(0, $result);

        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString('Info', $output);
        self::assertStringContainsString('Node Test', $output);
        self::assertStringContainsString('metrics', $output);
        self::assertStringContainsString('  ├ interval: 60', $output);
        self::assertStringContainsString('  └ items', $output);
        self::assertStringContainsString('    └ process.virtual.memory_max_bytes: -1', $output);
    }

    public function testNoData(): void
    {
        $this->centrifugo
            ->expects(self::once())
            ->method('info')
            ->willReturn(['nodes' => []])
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
            ->method('info')
            ->willThrowException(new \Exception('test', 5))
        ;

        $result = $this->commandTester->execute(['command' => $this->command->getName()]);
        self::assertSame(5, $result);

        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString('test', $output);
    }
}
