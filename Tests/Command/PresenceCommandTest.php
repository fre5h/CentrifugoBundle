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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class PresenceCommandTest extends TestCase
{
    /** @var CentrifugoInterface|MockObject */
    private $centrifugo;

    /** @var CentrifugoChecker|MockObject */
    private $centrifugoChecker;

    /** @var Command */
    private $command;

    /** @var Application */
    private $application;

    /** @var CommandTester */
    private $commandTester;

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

    public function testSuccessfulExecute(): void
    {
        $this->centrifugo
            ->expects(self::once())
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
                        ],
                    ],
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
        self::assertStringContainsString('Presence', $output);
        self::assertStringContainsString('client: c54313b2-0442-499a-a70c-051f8588020f', $output);
        self::assertStringContainsString('user: 42', $output);
        self::assertStringContainsString('conn_info', $output);
        self::assertStringContainsString('"username": "user1@test.com"', $output);
    }

    public function testNoData(): void
    {
        $this->centrifugo
            ->expects(self::once())
            ->method('presence')
            ->with('channelA')
            ->willReturn(['presence' => []])
        ;

        $result = $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'channel' => 'channelA',
            ]
        );
        self::assertSame(0, $result);

        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString('NO DATA', $output);
    }

    public function testException(): void
    {
        $this->centrifugo
            ->expects(self::once())
            ->method('presence')
            ->willThrowException(new \Exception('test', 5))
        ;

        $result = $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'channel' => 'channelA',
            ]
        );
        self::assertSame(5, $result);

        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString('test', $output);
    }
}
