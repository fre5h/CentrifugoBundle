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
use PHPUnit\Framework\TestCase;

/**
 * HistoryCommandTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class HistoryCommandTest extends TestCase
{
    public function testInterfaces(): void
    {
        $command = new HistoryCommand(channel: 'foo');
        self::assertInstanceOf(SerializableCommandInterface::class, $command);
        self::assertInstanceOf(CommandInterface::class, $command);
    }

    public function testConstructor(): void
    {
        $command = new HistoryCommand(channel: 'foo');
        self::assertEquals(Method::HISTORY, $command->getMethod());
        self::assertEquals(['channel' => 'foo'], $command->getParams());
        self::assertEquals(['foo'], $command->getChannels());
    }

    public function testSerializationRequiredData(): void
    {
        $command = new HistoryCommand(channel: 'foo');
        self::assertJsonStringEqualsJsonString(
            <<<'JSON'
                {
                    "method": "history",
                    "params": {
                        "channel": "foo"
                    }
                }
            JSON,
            \json_encode($command, \JSON_THROW_ON_ERROR | \JSON_FORCE_OBJECT)
        );
    }

    public function testSerializationAllData(): void
    {
        $command = new HistoryCommand(
            channel: 'foo',
            reverse: true,
            limit: 10,
            streamPosition: new StreamPosition(offset: 5, epoch: 'test'),
        );
        self::assertJsonStringEqualsJsonString(
            <<<'JSON'
                {
                    "method": "history",
                    "params": {
                        "channel": "foo",
                        "limit": 10,
                        "reverse": true,
                        "since": {
                            "epoch": "test",
                            "offset": 5
                        }
                    }
                }
            JSON,
            \json_encode($command, \JSON_THROW_ON_ERROR | \JSON_FORCE_OBJECT)
        );
    }

    public function testSerializationWithZeroValues(): void
    {
        $command = new HistoryCommand(
            channel: 'foo',
            reverse: true,
            limit: 0,
            streamPosition: new StreamPosition(offset: null, epoch: null),
        );
        self::assertJsonStringEqualsJsonString(
            <<<'JSON'
                {
                    "method": "history",
                    "params": {
                        "channel": "foo",
                        "reverse": true
                    }
                }
            JSON,
            \json_encode($command, \JSON_THROW_ON_ERROR | \JSON_FORCE_OBJECT)
        );
    }
}
