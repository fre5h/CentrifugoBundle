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
use Fresh\CentrifugoBundle\Model\RefreshCommand;
use Fresh\CentrifugoBundle\Model\Method;
use Fresh\CentrifugoBundle\Model\SerializableCommandInterface;
use PHPUnit\Framework\TestCase;

/**
 * RefreshCommandTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class RefreshCommandTest extends TestCase
{
    public function testInterfaces(): void
    {
        $command = new RefreshCommand(user: 'foo');
        self::assertInstanceOf(SerializableCommandInterface::class, $command);
        self::assertInstanceOf(CommandInterface::class, $command);
    }

    public function testConstructor(): void
    {
        $command = new RefreshCommand(user: 'foo');
        self::assertEquals(Method::REFRESH, $command->getMethod());
        self::assertEquals(['user' => 'foo'], $command->getParams());
        self::assertEquals([], $command->getChannels());
    }

    public function testSerializationRequiredData(): void
    {
        $command = new RefreshCommand(user: 'foo');
        self::assertJsonStringEqualsJsonString(
            <<<'JSON'
                {
                    "user": "foo"
                }
            JSON,
            \json_encode($command, \JSON_THROW_ON_ERROR | \JSON_FORCE_OBJECT)
        );
    }

    public function testSerializationAllData(): void
    {
        $command = new RefreshCommand(
            user: 'foo',
            client: 'clientID',
            session: 'sessionID',
            expired: true,
            expireAt: 1234567890,
        );
        self::assertJsonStringEqualsJsonString(
            <<<'JSON'
                {
                    "user": "foo",
                    "client": "clientID",
                    "session": "sessionID",
                    "expired": true,
                    "expire_at": 1234567890
                }
            JSON,
            \json_encode($command, \JSON_THROW_ON_ERROR | \JSON_FORCE_OBJECT)
        );
    }
}
