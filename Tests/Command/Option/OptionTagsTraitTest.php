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

use Fresh\CentrifugoBundle\Command\PublishCommand;
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
 * OptionTagsTraitTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class OptionTagsTraitTest extends TestCase
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

    #[Test]
    public function tagsIsNotValidJson(): void
    {
        $this->centrifugo
            ->expects($this->never())
            ->method('publish')
        ;

        $this->expectException(InvalidOptionException::class);
        $this->expectExceptionMessage('Option "--tags" is not a valid JSON.');

        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'data' => '{"foo":"bar"}',
                'channel' => 'channelName',
                '--tags' => 'invalid json',
            ],
        );
    }

    #[Test]
    public function tagsIsNotArray(): void
    {
        $this->centrifugo
            ->expects($this->never())
            ->method('publish')
        ;

        $this->expectException(InvalidOptionException::class);
        $this->expectExceptionMessage('Option "--tags" should be an associative array of strings.');

        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'data' => '{"foo":"bar"}',
                'channel' => 'channelName',
                '--tags' => 'true',
            ],
        );
    }

    #[Test]
    public function tagValueIsNotString(): void
    {
        $this->centrifugo
            ->expects($this->never())
            ->method('publish')
        ;

        $this->expectException(InvalidOptionException::class);
        $this->expectExceptionMessage('Option "--tags" should be an associative array of strings.');

        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'data' => '{"foo":"bar"}',
                'channel' => 'channelName',
                '--tags' => '{"foo":123}',
            ],
        );
    }

    #[Test]
    public function validTags(): void
    {
        $this->centrifugo
            ->expects($this->once())
            ->method('publish')
        ;

        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'data' => '{"foo":"bar"}',
                'channel' => 'channelName',
                '--tags' => '{"env":"test"}',
            ],
        );
    }
}
