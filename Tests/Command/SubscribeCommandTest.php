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

use Fresh\CentrifugoBundle\Command\SubscribeCommand;
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
 * SubscribeCommandTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class SubscribeCommandTest extends TestCase
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
        $command = new SubscribeCommand($this->centrifugo, $this->centrifugoChecker);

        $this->application = new Application();
        $this->application->add($command);

        $this->command = $this->application->find('centrifugo:subscribe');
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
            ->expects($this->once())
            ->method('subscribe')
            ->with('user123', 'channelA')
        ;

        $result = $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'user' => 'user123',
                'channel' => 'channelA',
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
            ->method('subscribe')
            ->with('user123', 'channelA', ['foo1' => 'bar1'], 'SGVsbG8gd29ybGQ=', 'clientID', 'sessionID', ['foo2' => 'bar2'], 'QmxhIGJsYSBibGE=', $this->isInstanceOf(StreamPosition::class))
        ;

        $result = $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'user' => 'user123',
                'channel' => 'channelA',
                '--info' => '{"foo1":"bar1"}',
                '--base64info' => 'SGVsbG8gd29ybGQ=',
                '--client' => 'clientID',
                '--session' => 'sessionID',
                '--data' => '{"foo2":"bar2"}',
                '--base64data' => 'QmxhIGJsYSBibGE=',
                '--offset' => 5,
                '--epoch' => 'test',
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
            ->method('subscribe')
            ->willThrowException(new \Exception('test'))
        ;

        $result = $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'user' => 'user123',
                'channel' => 'channelA',
            ],
        );
        $this->assertSame(1, $result);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('test', $output);
    }
}
