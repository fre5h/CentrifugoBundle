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

use Fresh\CentrifugoBundle\Command\PublishCommand;
use Fresh\CentrifugoBundle\Service\Centrifugo;
use Fresh\CentrifugoBundle\Service\CentrifugoChecker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class PublishCommandTest extends TestCase
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
        $command = new PublishCommand($this->centrifugo, $this->centrifugoChecker);

        $this->application = new Application();
        $this->application->add($command);

        $this->command = $this->application->find('centrifugo:publish');
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
            ->method('publish')
            ->with(['foo' => 'bar'], 'channelA')
        ;

        $result = $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'data' => '{"foo":"bar"}',
                'channel' => 'channelA',
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
            ->method('publish')
            ->willThrowException(new \Exception('test', 5))
        ;

        $result = $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'data' => '{"foo":"bar"}',
                'channel' => 'channelA',
            ]
        );
        self::assertSame(5, $result);

        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString('test', $output);
    }
}
