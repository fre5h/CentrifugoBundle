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
use Fresh\CentrifugoBundle\Model\PresenceStatsCommand;
use Fresh\CentrifugoBundle\Model\SerializableCommandInterface;
use PHPUnit\Framework\TestCase;

/**
 * PresenceStatsCommandTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class PresenceStatsCommandTest extends TestCase
{
    public function testInterfaces(): void
    {
        $command = new PresenceStatsCommand(channel: 'foo');
        self::assertInstanceOf(SerializableCommandInterface::class, $command);
        self::assertInstanceOf(CommandInterface::class, $command);
    }

    public function testConstructor(): void
    {
        $command = new PresenceStatsCommand(channel: 'foo');
        self::assertEquals(Method::PRESENCE_STATS, $command->getMethod());
        self::assertEquals(['channel' => 'foo'], $command->getParams());
        self::assertEquals(['foo'], $command->getChannels());
    }

    public function testSerialization(): void
    {
        $command = new PresenceStatsCommand(channel: 'foo');
        self::assertJsonStringEqualsJsonString(
            <<<'JSON'
                {
                    "method": "presence_stats",
                    "params": {
                        "channel": "foo"
                    }
                }
            JSON,
            \json_encode($command, \JSON_THROW_ON_ERROR | \JSON_FORCE_OBJECT)
        );
    }
}
