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
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * RefreshCommandTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class RefreshCommandTest extends TestCase
{
    #[Test]
    public function interfaces(): void
    {
        $command = new RefreshCommand(user: 'foo');
        $this->assertInstanceOf(SerializableCommandInterface::class, $command);
        $this->assertInstanceOf(CommandInterface::class, $command);
    }

    #[Test]
    public function constructor(): void
    {
        $command = new RefreshCommand(user: 'foo');
        $this->assertEquals(Method::REFRESH, $command->getMethod());
        $this->assertEquals(['user' => 'foo'], $command->getParams());
        $this->assertEquals([], $command->getChannels());
    }

    #[Test]
    public function serializationRequiredData(): void
    {
        $command = new RefreshCommand(user: 'foo');
        $this->assertJsonStringEqualsJsonString(
            <<<'JSON'
                {
                    "user": "foo"
                }
            JSON,
            \json_encode($command, \JSON_THROW_ON_ERROR | \JSON_FORCE_OBJECT),
        );
    }

    #[Test]
    public function serializationAllData(): void
    {
        $command = new RefreshCommand(
            user: 'foo',
            client: 'clientID',
            session: 'sessionID',
            expired: true,
            expireAt: 1234567890,
        );
        $this->assertJsonStringEqualsJsonString(
            <<<'JSON'
                {
                    "user": "foo",
                    "client": "clientID",
                    "session": "sessionID",
                    "expired": true,
                    "expire_at": 1234567890
                }
            JSON,
            \json_encode($command, \JSON_THROW_ON_ERROR | \JSON_FORCE_OBJECT),
        );
    }
}
