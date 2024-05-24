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
use Fresh\CentrifugoBundle\Model\PresenceCommand;
use Fresh\CentrifugoBundle\Model\SerializableCommandInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * PresenceCommandTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class PresenceCommandTest extends TestCase
{
    #[Test]
    public function interfaces(): void
    {
        $command = new PresenceCommand(channel: 'foo');
        self::assertInstanceOf(SerializableCommandInterface::class, $command);
        self::assertInstanceOf(CommandInterface::class, $command);
    }

    #[Test]
    public function constructor(): void
    {
        $command = new PresenceCommand(channel: 'foo');
        self::assertEquals(Method::PRESENCE, $command->getMethod());
        self::assertEquals(['channel' => 'foo'], $command->getParams());
        self::assertEquals(['foo'], $command->getChannels());
    }

    #[Test]
    public function serialization(): void
    {
        $command = new PresenceCommand(channel: 'foo');
        self::assertJsonStringEqualsJsonString(
            <<<'JSON'
                {
                    "channel": "foo"
                }
            JSON,
            \json_encode($command, \JSON_THROW_ON_ERROR | \JSON_FORCE_OBJECT)
        );
    }
}
