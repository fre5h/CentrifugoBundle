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
use Fresh\CentrifugoBundle\Model\Disconnect;
use Fresh\CentrifugoBundle\Model\DisconnectCommand;
use Fresh\CentrifugoBundle\Model\Method;
use Fresh\CentrifugoBundle\Model\SerializableCommandInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * DisconnectCommandTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class DisconnectCommandTest extends TestCase
{
    #[Test]
    public function interfaces(): void
    {
        $command = new DisconnectCommand(user: 'foo');
        $this->assertInstanceOf(SerializableCommandInterface::class, $command);
        $this->assertInstanceOf(CommandInterface::class, $command);
    }

    #[Test]
    public function constructor(): void
    {
        $command = new DisconnectCommand(user: 'foo');
        $this->assertEquals(Method::DISCONNECT, $command->getMethod());
        $this->assertEquals(['user' => 'foo'], $command->getParams());
        $this->assertEquals([], $command->getChannels());
    }

    #[Test]
    public function serializationRequiredData(): void
    {
        $command = new DisconnectCommand(user: 'foo');
        $this->assertJsonStringEqualsJsonString(
            <<<'JSON'
                {
                    "user": "foo"
                }
            JSON,
            json_encode($command, \JSON_THROW_ON_ERROR | \JSON_FORCE_OBJECT),
        );
    }

    #[Test]
    public function serializationAllData(): void
    {
        $command = new DisconnectCommand(
            user: 'foo',
            clientIdWhitelist: ['clientID1'],
            client: 'clientID2',
            session: 'sessionID1',
            disconnectObject: new Disconnect(code: 999, reason: 'some reason'),
        );
        $this->assertJsonStringEqualsJsonString(
            <<<'JSON'
                {
                    "user": "foo",
                    "whitelist": ["clientID1"],
                    "client": "clientID2",
                    "session": "sessionID1",
                    "disconnect": {
                        "code": 999,
                        "reason": "some reason"
                    }
                }
            JSON,
            json_encode($command, \JSON_THROW_ON_ERROR),
        );
    }
}
