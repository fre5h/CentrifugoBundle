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
use Fresh\CentrifugoBundle\Model\Method;
use Fresh\CentrifugoBundle\Model\SerializableCommandInterface;
use Fresh\CentrifugoBundle\Model\UnsubscribeCommand;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * UnsubscribeCommandTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class UnsubscribeCommandTest extends TestCase
{
    #[Test]
    public function interfaces(): void
    {
        $command = new UnsubscribeCommand(
            user: 'bar',
            channel: 'foo',
        );
        self::assertInstanceOf(SerializableCommandInterface::class, $command);
        self::assertInstanceOf(CommandInterface::class, $command);
    }

    #[Test]
    public function constructor(): void
    {
        $command = new UnsubscribeCommand(
            user: 'bar',
            channel: 'foo',
        );
        self::assertEquals(Method::UNSUBSCRIBE, $command->getMethod());
        self::assertEquals(['channel' => 'foo', 'user' => 'bar'], $command->getParams());
        self::assertEquals(['foo'], $command->getChannels());
    }

    #[Test]
    public function serializationRequiredData(): void
    {
        $command = new UnsubscribeCommand(
            user: 'bar',
            channel: 'foo',
        );
        self::assertJsonStringEqualsJsonString(
            <<<'JSON'
                {
                    "channel": "foo",
                    "user": "bar"
                }
            JSON,
            \json_encode($command, \JSON_THROW_ON_ERROR | \JSON_FORCE_OBJECT)
        );
    }

    #[Test]
    public function serializationAllData(): void
    {
        $command = new UnsubscribeCommand(
            user: 'bar',
            channel: 'foo',
            client: 'abc',
            session: 'qwerty',
        );
        self::assertJsonStringEqualsJsonString(
            <<<'JSON'
                {
                    "channel": "foo",
                    "user": "bar",
                    "client": "abc",
                    "session": "qwerty"
                }
            JSON,
            \json_encode($command, \JSON_THROW_ON_ERROR | \JSON_FORCE_OBJECT)
        );
    }
}
