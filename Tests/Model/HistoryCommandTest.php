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

use Fresh\CentrifugoBundle\Model\CommandInterface;
use Fresh\CentrifugoBundle\Model\HistoryCommand;
use Fresh\CentrifugoBundle\Model\Method;
use Fresh\CentrifugoBundle\Model\SerializableCommandInterface;
use Fresh\CentrifugoBundle\Model\StreamPosition;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * HistoryCommandTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class HistoryCommandTest extends TestCase
{
    #[Test]
    public function interfaces(): void
    {
        $command = new HistoryCommand(channel: 'foo');
        $this->assertInstanceOf(SerializableCommandInterface::class, $command);
        $this->assertInstanceOf(CommandInterface::class, $command);
    }

    #[Test]
    public function constructor(): void
    {
        $command = new HistoryCommand(channel: 'foo');
        $this->assertEquals(Method::HISTORY, $command->getMethod());
        $this->assertEquals(['channel' => 'foo'], $command->getParams());
        $this->assertEquals(['foo'], $command->getChannels());
    }

    #[Test]
    public function serializationRequiredData(): void
    {
        $command = new HistoryCommand(channel: 'foo');
        $this->assertJsonStringEqualsJsonString(
            <<<'JSON'
                {
                    "channel": "foo"
                }
            JSON,
            \json_encode($command, \JSON_THROW_ON_ERROR | \JSON_FORCE_OBJECT)
        );
    }

    #[Test]
    public function serializationAllData(): void
    {
        $command = new HistoryCommand(
            channel: 'foo',
            reverse: true,
            limit: 10,
            streamPosition: new StreamPosition(offset: 5, epoch: 'test'),
        );
        $this->assertJsonStringEqualsJsonString(
            <<<'JSON'
                {
                    "channel": "foo",
                    "reverse": true,
                    "limit": 10,
                    "since": {
                        "offset": 5,
                        "epoch": "test"
                    }
                }
            JSON,
            \json_encode($command, \JSON_THROW_ON_ERROR | \JSON_FORCE_OBJECT)
        );
    }

    #[Test]
    public function serializationWithZeroValues(): void
    {
        $command = new HistoryCommand(
            channel: 'foo',
            reverse: true,
            limit: 0,
            streamPosition: new StreamPosition(offset: null, epoch: null),
        );
        $this->assertJsonStringEqualsJsonString(
            <<<'JSON'
                {
                    "channel": "foo",
                    "reverse": true
                }
            JSON,
            \json_encode($command, \JSON_THROW_ON_ERROR | \JSON_FORCE_OBJECT)
        );
    }

    #[Test]
    public function processResponse(): void
    {
        $command = new HistoryCommand(
            channel: 'foo',
            reverse: true,
            limit: 0,
            streamPosition: new StreamPosition(offset: null, epoch: null),
        );
        $this->assertEquals(['foo' => 'bar'], $command->processResponse(['result' => ['foo' => 'bar']]));
    }
}
