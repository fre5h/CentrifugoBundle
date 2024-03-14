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
use Fresh\CentrifugoBundle\Model\Override;
use Fresh\CentrifugoBundle\Model\SubscribeCommand;
use Fresh\CentrifugoBundle\Model\Method;
use Fresh\CentrifugoBundle\Model\SerializableCommandInterface;
use Fresh\CentrifugoBundle\Model\StreamPosition;
use PHPUnit\Framework\TestCase;

/**
 * SubscribeCommandTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class SubscribeCommandTest extends TestCase
{
    public function testInterfaces(): void
    {
        $command = new SubscribeCommand(
            user: 'user123',
            channel: 'foo',
        );
        self::assertInstanceOf(SerializableCommandInterface::class, $command);
        self::assertInstanceOf(CommandInterface::class, $command);
    }

    public function testConstructor(): void
    {
        $command = new SubscribeCommand(
            user: 'user123',
            channel: 'foo',
        );
        self::assertEquals(Method::SUBSCRIBE, $command->getMethod());
        self::assertEquals(['user' => 'user123', 'channel' => 'foo'], $command->getParams());
        self::assertEquals(['foo'], $command->getChannels());
    }

    public function testSerializationRequiredData(): void
    {
        $command = new SubscribeCommand(
            user: 'user123',
            channel: 'foo',
        );
        self::assertJsonStringEqualsJsonString(
            <<<'JSON'
                {
                    "method": "subscribe",
                    "params": {
                        "channel": "foo",
                        "user": "user123"
                    }
                }
            JSON,
            \json_encode($command, \JSON_THROW_ON_ERROR | \JSON_FORCE_OBJECT)
        );
    }

    public function testSerializationAllData(): void
    {
        $command = new SubscribeCommand(
            user: 'user123',
            channel: 'foo',
            info: ['foo' => 'bar'],
            base64Info: 'qwerty',
            client: 'clientID',
            session: 'sessionID',
            data: ['abc' => 'def'],
            base64Data: '12345',
            recoverSince: new StreamPosition(offset: 5, epoch: 'test'),
            override: new Override(
                presence: true,
                joinLeave: false,
                forcePushJoinLeave: true,
                forcePositioning: false,
                forceRecovery: true,
            ),
        );
        self::assertJsonStringEqualsJsonString(
            <<<'JSON'
                {
                    "method": "subscribe",
                    "params": {
                        "b64data": "12345",
                        "b64info": "qwerty",
                        "channel": "foo",
                        "client": "clientID",
                        "data": {
                            "abc": "def"
                        },
                        "info": {
                            "foo": "bar"
                        },
                        "override": {
                            "force_positioning": false,
                            "force_push_join_leave": true,
                            "force_recovery": true,
                            "join_leave": false,
                            "presence": true
                        },
                        "recover_since": {
                            "epoch": "test",
                            "offset": 5
                        },
                        "session": "sessionID",
                        "user": "user123"
                    }
                }
            JSON,
            \json_encode($command, \JSON_THROW_ON_ERROR | \JSON_FORCE_OBJECT)
        );
    }

    public function testSerializationWithZeroValues(): void
    {
        $command = new SubscribeCommand(
            user: 'user123',
            channel: 'foo',
            info: ['foo' => 'bar'],
            base64Info: '',
            client: 'clientID',
            session: 'sessionID',
            data: ['abc' => 'def'],
            base64Data: '',
            recoverSince: new StreamPosition(offset: null, epoch: null),
        );
        self::assertJsonStringEqualsJsonString(
            <<<'JSON'
                {
                    "method": "subscribe",
                    "params": {
                        "channel": "foo",
                        "client": "clientID",
                        "data": {
                            "abc": "def"
                        },
                        "info": {
                            "foo": "bar"
                        },
                        "session": "sessionID",
                        "user": "user123"
                    }
                }
            JSON,
            \json_encode($command, \JSON_THROW_ON_ERROR | \JSON_FORCE_OBJECT)
        );
    }
}
