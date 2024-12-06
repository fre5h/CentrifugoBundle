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

namespace Fresh\CentrifugoBundle\Tests\Command\Option;

use Fresh\CentrifugoBundle\Command\SubscribeCommand;
use Fresh\CentrifugoBundle\Service\CentrifugoChecker;
use Fresh\CentrifugoBundle\Service\CentrifugoInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * OptionBase64InfoTraitTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class OptionBase64InfoTraitTest extends TestCase
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
    public function optionIsNotValidBase64(): void
    {
        $this->centrifugo
            ->expects($this->never())
            ->method('subscribe')
        ;

        $this->expectException(InvalidOptionException::class);
        $this->expectExceptionMessage('Option "--base64info" should be a valid base64 encoded string.');

        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'user' => 'user123',
                'channel' => 'channelName',
                '--base64info' => 'SGVsbG8gd29ybGQ=bla',
            ],
        );
    }

    #[Test]
    public function validOption(): void
    {
        $this->centrifugo
            ->expects($this->once())
            ->method('subscribe')
        ;

        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'user' => 'user123',
                'channel' => 'channelName',
                '--base64info' => 'SGVsbG8gd29ybGQ=',
            ],
        );
    }
}
