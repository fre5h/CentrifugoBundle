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

namespace Fresh\CentrifugoBundle\Tests\Model;

use Fresh\CentrifugoBundle\Exception\UnexpectedValueException;
use Fresh\CentrifugoBundle\Model\BatchRequest;
use Fresh\CentrifugoBundle\Model\BroadcastCommand;
use Fresh\CentrifugoBundle\Model\InfoCommand;
use Fresh\CentrifugoBundle\Model\PublishCommand;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * BatchRequestTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class BatchRequestTest extends TestCase
{
    private BatchRequest $command;

    protected function setUp(): void
    {
        $publishCommand = new PublishCommand(data: ['foo' => 'bar'], channel: 'channelA');
        $broadcastCommand = new BroadcastCommand(data: ['baz' => 'qux'], channels: ['channelB', 'channelC']);
        $this->command = new BatchRequest(commands: [$publishCommand, $broadcastCommand]);
    }

    protected function tearDown(): void
    {
        unset($this->command);
    }

    #[Test]
    public function getCommands(): void
    {
        $commands = $this->command->getCommands();
        $this->assertInstanceOf(PublishCommand::class, $commands->current());

        $commands->next();
        $this->assertInstanceOf(BroadcastCommand::class, $commands->current());
    }

    #[Test]
    public function addCommandAndGetNumberOfCommands(): void
    {
        $this->assertEquals(2, $this->command->getNumberOfCommands());
        $this->command->addCommand(new InfoCommand());
        $this->assertEquals(3, $this->command->getNumberOfCommands());
        $commands = $this->command->getCommands();
        $commands->next();
        $commands->next();

        $this->assertInstanceOf(InfoCommand::class, $commands->current());
    }

    #[Test]
    public function constructorWithException(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Invalid command for batch request. Only instances of Fresh\CentrifugoBundle\Model\CommandInterface are allowed.');

        new BatchRequest([new \stdClass()]);
    }

    #[Test]
    public function getChannels(): void
    {
        $channels = $this->command->getChannels();
        $this->assertEquals('channelA', $channels->current());

        $channels->next();
        $this->assertEquals('channelB', $channels->current());

        $channels->next();
        $this->assertEquals('channelC', $channels->current());
    }

    #[Test]
    public function prepareLineDelimitedJsonWithEmptyBatchRequest(): void
    {
        $batchRequest = new BatchRequest();
        $this->assertEquals('{}', $batchRequest->prepareLineDelimitedJson());
    }

    #[Test]
    public function prepareLineDelimitedJsonWithNonEmptyBatchRequest(): void
    {
        $this->assertJsonStringEqualsJsonString(
            expectedJson: <<<'JSON'
                {
                    "commands": [
                        {
                            "publish": {
                                "channel": "channelA",
                                "data": {
                                    "foo": "bar"
                                }
                            }
                        },
                        {
                            "broadcast": {
                                "channels": ["channelB", "channelC"],
                                "data": {
                                    "baz": "qux"
                                }
                            }
                        }
                    ]
                }
            JSON,
            actualJson: $this->command->prepareLineDelimitedJson(),
        );
    }
}
