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

namespace Fresh\CentrifugoBundle\Tests\Command\Argument;

use Fresh\CentrifugoBundle\Command\BroadcastCommand;
use Fresh\CentrifugoBundle\Service\CentrifugoChecker;
use Fresh\CentrifugoBundle\Service\CentrifugoInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * ArgumentDataTraitTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class ArgumentDataTraitTest extends TestCase
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
        $command = new BroadcastCommand($this->centrifugo, $this->centrifugoChecker);

        $this->application = new Application();
        $this->application->add($command);

        $this->command = $this->application->find('centrifugo:broadcast');
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
    public function dataIsNotValidJson(): void
    {
        $this->centrifugo
            ->expects(self::never())
            ->method('broadcast')
        ;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument "data" is not a valid JSON.');

        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'data' => 'invalid json',
                'channels' => ['channelA', 'channelB'],
            ]
        );
    }

    #[Test]
    public function dataIsNotString(): void
    {
        $this->centrifugo
            ->expects(self::never())
            ->method('broadcast')
        ;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument "data" is not a string.');

        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'data' => ['foo'],
                'channels' => ['channelA', 'channelB'],
            ]
        );
    }
}
