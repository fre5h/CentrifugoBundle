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
use Fresh\CentrifugoBundle\Model\DisconnectCommand;
use Fresh\CentrifugoBundle\Model\Disconnect;
use Fresh\CentrifugoBundle\Model\Method;
use Fresh\CentrifugoBundle\Model\SerializableCommandInterface;
use PHPUnit\Framework\TestCase;

/**
 * DisconnectCommandTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class DisconnectCommandTest extends TestCase
{
    public function testInterfaces(): void
    {
        $command = new DisconnectCommand(user: 'foo');
        self::assertInstanceOf(SerializableCommandInterface::class, $command);
        self::assertInstanceOf(CommandInterface::class, $command);
    }

    public function testConstructor(): void
    {
        $command = new DisconnectCommand(user: 'foo');
        self::assertEquals(Method::DISCONNECT, $command->getMethod());
        self::assertEquals(['user' => 'foo'], $command->getParams());
        self::assertEquals([], $command->getChannels());
    }

    public function testSerializationRequiredData(): void
    {
        $command = new DisconnectCommand(user: 'foo');
        self::assertJsonStringEqualsJsonString(
            <<<'JSON'
                {
                    "method": "disconnect",
                    "params": {
                        "user": "foo"
                    }
                }
            JSON,
            \json_encode($command, \JSON_THROW_ON_ERROR | \JSON_FORCE_OBJECT)
        );
    }

    public function testSerializationAllData(): void
    {
        $command = new DisconnectCommand(
            user: 'foo',
            clientIdWhitelist: ['clientID1'],
            client: 'clientID2',
            session: 'sessionID1',
            disconnectObject: new Disconnect(code: 999, reason: 'some reason'),
        );
        self::assertJsonStringEqualsJsonString(
            <<<'JSON'
                {
                    "method": "disconnect",
                    "params": {
                        "user": "foo",
                        "whitelist": ["clientID1"],
                        "client": "clientID2",
                        "session": "sessionID1",
                        "disconnect": {
                            "code": 999,
                            "reason": "some reason"
                        }
                    }
                }
            JSON,
            \json_encode($command, \JSON_THROW_ON_ERROR | \JSON_FORCE_OBJECT)
        );
    }
}
