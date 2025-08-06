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
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * BroadcastCommandTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class BroadcastCommandTest extends TestCase
{
    #[Test]
    public function interfaces(): void
    {
        $command = new BroadcastCommand(
            data: ['baz' => 'qux'],
            channels: ['foo', 'bar'],
        );
        $this->assertInstanceOf(SerializableCommandInterface::class, $command);
        $this->assertInstanceOf(CommandInterface::class, $command);
    }

    #[Test]
    public function constructor(): void
    {
        $command = new BroadcastCommand(
            data: ['baz' => 'qux'],
            channels: ['foo', 'bar'],
        );
        $this->assertEquals(Method::BROADCAST, $command->getMethod());
        $this->assertEquals(['channels' => ['foo', 'bar'], 'data' => ['baz' => 'qux']], $command->getParams());
        $this->assertEquals(['foo', 'bar'], $command->getChannels());
    }

    #[Test]
    public function serializationRequiredData(): void
    {
        $command = new BroadcastCommand(
            data: ['baz' => 'qux'],
            channels: ['foo', 'bar'],
        );
        $this->assertJsonStringEqualsJsonString(
            <<<'JSON'
                {
                    "channels": ["foo", "bar"],
                    "data": {
                        "baz": "qux"
                    }
                }
            JSON,
            \json_encode($command, \JSON_THROW_ON_ERROR),
        );
    }

    #[Test]
    public function serializationAllData(): void
    {
        $command = new BroadcastCommand(
            data: ['baz' => 'qux'],
            channels: ['foo', 'bar'],
            skipHistory: true,
            tags: ['tag' => 'value'],
            base64data: 'qwerty',
        );
        $this->assertJsonStringEqualsJsonString(
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
            \json_encode($command, \JSON_THROW_ON_ERROR),
        );
    }
}
