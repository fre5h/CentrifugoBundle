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

use Fresh\CentrifugoBundle\Model\BroadcastCommand;
use Fresh\CentrifugoBundle\Model\CommandInterface;
use Fresh\CentrifugoBundle\Model\Method;
use Fresh\CentrifugoBundle\Model\SerializableCommandInterface;
use PHPUnit\Framework\TestCase;

/**
 * BroadcastCommandTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class BroadcastCommandTest extends TestCase
{
    public function testInterfaces(): void
    {
        $command = new BroadcastCommand(
            data: ['baz' => 'qux'],
            channels: ['foo', 'bar'],
        );
        self::assertInstanceOf(SerializableCommandInterface::class, $command);
        self::assertInstanceOf(CommandInterface::class, $command);
    }

    public function testConstructor(): void
    {
        $command = new BroadcastCommand(
            data: ['baz' => 'qux'],
            channels: ['foo', 'bar'],
        );
        self::assertEquals(Method::BROADCAST, $command->getMethod());
        self::assertEquals(['channels' => ['foo', 'bar'], 'data' => ['baz' => 'qux']], $command->getParams());
        self::assertEquals(['foo', 'bar'], $command->getChannels());
    }

    public function testSerializationRequiredData(): void
    {
        $command = new BroadcastCommand(
            data: ['baz' => 'qux'],
            channels: ['foo', 'bar'],
        );
        self::assertJsonStringEqualsJsonString(
            <<<'JSON'
                {
                    "channels": ["foo", "bar"],
                    "data": {
                        "baz": "qux"
                    }
                }
            JSON,
            \json_encode($command, \JSON_THROW_ON_ERROR | \JSON_FORCE_OBJECT)
        );
    }

    public function testSerializationAllData(): void
    {
        $command = new BroadcastCommand(
            data: ['baz' => 'qux'],
            channels: ['foo', 'bar'],
            skipHistory: true,
            tags: ['tag' => 'value'],
            base64data: 'qwerty',
        );
        self::assertJsonStringEqualsJsonString(
            <<<'JSON'
                {
                    "channels": ["foo", "bar"],
                    "data": {
                        "baz": "qux"
                    },
                    "skip_history": true,
                    "tags": {
                        "tag": "value"
                    },
                    "base64data": "qwerty"
                }
            JSON,
            \json_encode($command, \JSON_THROW_ON_ERROR | \JSON_FORCE_OBJECT)
        );
    }
}
