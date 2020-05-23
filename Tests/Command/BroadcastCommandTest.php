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

use Fresh\CentrifugoBundle\Command\BroadcastCommand;
use Fresh\CentrifugoBundle\Exception\InvalidArgumentException as CentrifugoInvalidArgumentException;
use Fresh\CentrifugoBundle\Service\Centrifugo;
use Fresh\CentrifugoBundle\Service\CentrifugoChecker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Tester\CommandTester;

final class BroadcastCommandTest extends TestCase
{
    /** @var Centrifugo|MockObject */
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
        $this->centrifugo = $this->createMock(Centrifugo::class);
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

    public function testSuccessfulExecute(): void
    {
        $this->centrifugo
            ->expects(self::once())
            ->method('broadcast')
            ->with(['foo' => 'bar'], ['channelA', 'channelB'])
        ;

        $result = $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'data' => '{"foo":"bar"}',
                'channels' => ['channelA', 'channelB'],
            ]
        );
        self::assertSame(0, $result);

        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString('DONE', $output);
    }

    public function testException(): void
    {
        $this->centrifugo
            ->expects(self::once())
            ->method('broadcast')
            ->willThrowException(new \Exception('test', 5))
        ;

        $result = $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'data' => '{"foo":"bar"}',
                'channels' => ['channelA', 'channelB'],
            ]
        );
        self::assertSame(5, $result);

        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString('test', $output);
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
            ->method('broadcast')
        ;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('test');

        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'data' => '{"foo":"bar"}',
                'channels' => ['channelA', 'channelB'],
            ]
        );
    }
}
