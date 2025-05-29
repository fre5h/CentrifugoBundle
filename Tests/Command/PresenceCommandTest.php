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

use Fresh\CentrifugoBundle\Command\PresenceCommand;
use Fresh\CentrifugoBundle\Service\CentrifugoChecker;
use Fresh\CentrifugoBundle\Service\CentrifugoInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * PresenceCommandTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class PresenceCommandTest extends TestCase
{
    private CentrifugoInterface&MockObject $centrifugo;
    private CentrifugoChecker&MockObject $centrifugoChecker;
    private Command $command;
    private Application $application;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->centrifugo = $this->createMock(CentrifugoInterface::class);
        $this->centrifugoChecker = $this->createMock(CentrifugoChecker::class);
        $command = new PresenceCommand($this->centrifugo, $this->centrifugoChecker);

        $this->application = new Application();
        $this->application->add($command);

        $this->command = $this->application->find('centrifugo:presence');
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
    public function successfulExecution(): void
    {
        $this->centrifugo
            ->expects($this->once())
            ->method('presence')
            ->with('channelA')
            ->willReturn(
                [
                    'presence' => [
                        'c54313b2-0442-499a-a70c-051f8588020f' => [
                            'client' => 'c54313b2-0442-499a-a70c-051f8588020f',
                            'user' => '42',
                            'conn_info' => [
                                'username' => 'user1@test.com',
                            ],
                            'chan_info' => [
                                'foo' => 'bar',
                            ],
                        ],
                    ],
                ],
            )
        ;

        $result = $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'channel' => 'channelA',
            ],
        );
        $this->assertSame(0, $result);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Presence', $output);
        $this->assertStringContainsString('client: c54313b2-0442-499a-a70c-051f8588020f', $output);
        $this->assertStringContainsString('user: 42', $output);
        $this->assertStringContainsString('conn_info', $output);
        $this->assertStringContainsString('chan_info', $output);
        $this->assertStringContainsString('"username": "user1@test.com"', $output);
    }

    #[Test]
    public function noData(): void
    {
        $this->centrifugo
            ->expects($this->once())
            ->method('presence')
            ->with('channelA')
            ->willReturn(['presence' => []])
        ;

        $result = $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'channel' => 'channelA',
            ],
        );
        $this->assertSame(0, $result);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('NO DATA', $output);
    }

    #[Test]
    public function exception(): void
    {
        $this->centrifugo
            ->expects($this->once())
            ->method('presence')
            ->willThrowException(new \Exception('test'))
        ;

        $result = $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'channel' => 'channelA',
            ],
        );
        $this->assertSame(1, $result);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('test', $output);
    }
}
