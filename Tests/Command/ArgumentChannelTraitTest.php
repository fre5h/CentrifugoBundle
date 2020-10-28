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
use Fresh\CentrifugoBundle\Exception\InvalidArgumentException as CentrifugoInvalidArgumentException;
use Fresh\CentrifugoBundle\Service\CentrifugoChecker;
use Fresh\CentrifugoBundle\Service\CentrifugoInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Tester\CommandTester;

final class ArgumentChannelTraitTest extends TestCase
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

    public function testInvalidChannelName(): void
    {
        $this->centrifugoChecker
            ->expects(self::once())
            ->method('assertValidChannelName')
            ->with('channelA')
            ->willThrowException(new CentrifugoInvalidArgumentException('test'))
        ;

        $this->centrifugo
            ->expects(self::never())
            ->method('presence')
        ;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('test');

        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'channel' => 'channelA',
            ]
        );
    }

    public function testChannelNameIsNotString(): void
    {
        $this->centrifugoChecker
            ->expects(self::never())
            ->method('assertValidChannelName')
        ;

        $this->centrifugo
            ->expects(self::never())
            ->method('presence')
        ;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument "channel" is not a string.');

        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'channel' => ['channelA'],
            ]
        );
    }
}
