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
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * ChannelsCommandTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class ChannelsCommandTest extends TestCase
{
    #[Test]
    public function interfaces(): void
    {
        $command = new ChannelsCommand();
        self::assertInstanceOf(SerializableCommandInterface::class, $command);
        self::assertInstanceOf(CommandInterface::class, $command);
    }

    #[Test]
    public function constructor(): void
    {
        $command = new ChannelsCommand();
        self::assertEquals(Method::CHANNELS, $command->getMethod());
        self::assertEquals([], $command->getParams());
        self::assertEquals([], $command->getChannels());
    }

    #[Test]
    public function serializationWithoutPattern(): void
    {
        $command = new ChannelsCommand();
        self::assertJsonStringEqualsJsonString(
            <<<'JSON'
                {}
            JSON,
            \json_encode($command, \JSON_THROW_ON_ERROR | \JSON_FORCE_OBJECT)
        );
    }

    #[Test]
    public function serializationWithPattern(): void
    {
        $command = new ChannelsCommand(pattern: 'abc');

        self::assertJsonStringEqualsJsonString(
            <<<'JSON'
                {
                    "pattern": "abc"
                }
            JSON,
            \json_encode($command, \JSON_THROW_ON_ERROR | \JSON_FORCE_OBJECT)
        );
    }
}
