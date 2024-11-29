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
use Fresh\CentrifugoBundle\Model\PresenceStatsCommand;
use Fresh\CentrifugoBundle\Model\SerializableCommandInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * PresenceStatsCommandTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class PresenceStatsCommandTest extends TestCase
{
    #[Test]
    public function interfaces(): void
    {
        $command = new PresenceStatsCommand(channel: 'foo');
        $this->assertInstanceOf(SerializableCommandInterface::class, $command);
        $this->assertInstanceOf(CommandInterface::class, $command);
    }

    #[Test]
    public function constructor(): void
    {
        $command = new PresenceStatsCommand(channel: 'foo');
        $this->assertEquals(Method::PRESENCE_STATS, $command->getMethod());
        $this->assertEquals(['channel' => 'foo'], $command->getParams());
        $this->assertEquals(['foo'], $command->getChannels());
    }

    #[Test]
    public function serialization(): void
    {
        $command = new PresenceStatsCommand(channel: 'foo');
        $this->assertJsonStringEqualsJsonString(
            <<<'JSON'
                {
                    "channel": "foo"
                }
            JSON,
            \json_encode($command, \JSON_THROW_ON_ERROR | \JSON_FORCE_OBJECT),
        );
    }

    #[Test]
    public function processResponse(): void
    {
        $command = new PresenceStatsCommand(channel: 'foo');
        $this->assertEquals(['foo' => 'bar'], $command->processResponse(['result' => ['foo' => 'bar']]));
    }
}
