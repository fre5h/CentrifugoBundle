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

use Fresh\CentrifugoBundle\Command\HistoryCommand;
use Fresh\CentrifugoBundle\Model\StreamPosition;
use Fresh\CentrifugoBundle\Service\CentrifugoChecker;
use Fresh\CentrifugoBundle\Service\CentrifugoInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * HistoryCommandTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class HistoryCommandTest extends TestCase
{
    /** @var CentrifugoInterface|MockObject */
    private CentrifugoInterface|MockObject $centrifugo;

    /** @var CentrifugoChecker|MockObject */
    private CentrifugoChecker|MockObject $centrifugoChecker;

    private Command $command;
    private Application $application;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->centrifugo = $this->createMock(CentrifugoInterface::class);
        $this->centrifugoChecker = $this->createMock(CentrifugoChecker::class);
        $command = new HistoryCommand($this->centrifugo, $this->centrifugoChecker);

        $this->application = new Application();
        $this->application->add($command);

        $this->command = $this->application->find('centrifugo:history');
        $this->commandTester = new CommandTester($this->command);
    }

    protected function tearDown(): void
    {
        unset(
            $this->centrifugo,
            $this->centrifugoChecker,
            $this->command,
            $this->application,
            $this->commandTester,
        );
    }

    #[Test]
    public function successfulExecutionWithRequiredParameters(): void
    {
        $this->centrifugo
            ->expects(self::once())
            ->method('history')
            ->with('channelA')
            ->willReturn(
                [
                    'publications' => [
                        [
                            'data' => [
                                'foo' => 'bar',
                            ],
                        ],
                    ],
                    'offset' => 0,
                    'epoch' => 'test',
                ]
            )
        ;

        $result = $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'channel' => 'channelA',
            ]
        );
        self::assertSame(0, $result);

        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString('Publications', $output);
        self::assertStringContainsString(
            <<<'JSON'
{
    "foo": "bar"
}
JSON,
            $output
        );
        self::assertStringContainsString('Offset: 0', $output);
        self::assertStringContainsString('Epoch: test', $output);
    }

    #[Test]
    public function successfulExecutionWithAllParameters(): void
    {
        $this->centrifugo
            ->expects(self::once())
            ->method('history')
            ->with('channelA', true, 10, self::isInstanceOf(StreamPosition::class))
            ->willReturn(
                [
                    'publications' => [
                        [
                            'data' => [
                                'foo' => 'bar',
                            ],
                        ],
                    ],
                    'offset' => 0,
                    'epoch' => 'test',
                ]
            )
        ;

        $result = $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'channel' => 'channelA',
                '--limit' => 10,
                '--offset' => 5,
                '--epoch' => 'test',
                '--reverse' => true,
            ]
        );
        self::assertSame(0, $result);

        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString('Publications', $output);
        self::assertStringContainsString(
            <<<'JSON'
{
    "foo": "bar"
}
JSON,
            $output
        );
        self::assertStringContainsString('Offset: 0', $output);
        self::assertStringContainsString('Epoch: test', $output);
    }

    #[Test]
    public function exception(): void
    {
        $this->centrifugo
            ->expects(self::once())
            ->method('history')
            ->willThrowException(new \Exception('test'))
        ;

        $result = $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'channel' => 'channelA',
            ]
        );
        self::assertSame(1, $result);

        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString('test', $output);
    }
}
