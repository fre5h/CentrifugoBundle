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

use Fresh\CentrifugoBundle\Model\ChannelsCommand;
use Fresh\CentrifugoBundle\Model\CommandInterface;
use Fresh\CentrifugoBundle\Model\Method;
use Fresh\CentrifugoBundle\Model\SerializableCommandInterface;
use PHPUnit\Framework\TestCase;

/**
 * ChannelsCommandTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class ChannelsCommandTest extends TestCase
{
    public function testInterfaces(): void
    {
        $command = new ChannelsCommand();
        self::assertInstanceOf(SerializableCommandInterface::class, $command);
        self::assertInstanceOf(CommandInterface::class, $command);
    }

    public function testConstructor(): void
    {
        $command = new ChannelsCommand();
        self::assertEquals(Method::CHANNELS, $command->getMethod());
        self::assertEquals([], $command->getParams());
        self::assertEquals([], $command->getChannels());
    }

    public function testSerializationWithoutPattern(): void
    {
        $command = new ChannelsCommand();
        self::assertJsonStringEqualsJsonString(
            <<<'JSON'
                {
                    "method": "channels",
                    "params": {}
                }
            JSON,
            \json_encode($command, \JSON_THROW_ON_ERROR | \JSON_FORCE_OBJECT)
        );
    }

    public function testSerializationWithPattern(): void
    {
        $command = new ChannelsCommand(pattern: 'abc');

        self::assertJsonStringEqualsJsonString(
            <<<'JSON'
                {
                    "method": "channels",
                    "params": {
                        "pattern": "abc"
                    }
                }
            JSON,
            \json_encode($command, \JSON_THROW_ON_ERROR | \JSON_FORCE_OBJECT)
        );
    }
}
