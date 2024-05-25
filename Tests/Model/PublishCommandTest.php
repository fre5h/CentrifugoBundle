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
use Fresh\CentrifugoBundle\Model\PublishCommand;
use Fresh\CentrifugoBundle\Model\SerializableCommandInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * PublishCommandTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class PublishCommandTest extends TestCase
{
    #[Test]
    public function interfaces(): void
    {
        $command = new PublishCommand(
            data: ['bar' => 'baz'],
            channel: 'foo',
        );
        self::assertInstanceOf(SerializableCommandInterface::class, $command);
        self::assertInstanceOf(CommandInterface::class, $command);
    }

    #[Test]
    public function constructor(): void
    {
        $command = new PublishCommand(
            data: ['bar' => 'baz'],
            channel: 'foo',
        );
        self::assertEquals(Method::PUBLISH, $command->getMethod());
        self::assertEquals(['channel' => 'foo', 'data' => ['bar' => 'baz']], $command->getParams());
        self::assertEquals(['foo'], $command->getChannels());
    }

    #[Test]
    public function serializationRequiredData(): void
    {
        $command = new PublishCommand(
            data: ['bar' => 'baz'],
            channel: 'foo',
        );
        self::assertJsonStringEqualsJsonString(
            <<<'JSON'
                {
                    "channel": "foo",
                    "data": {
                        "bar": "baz"
                    }
                }
            JSON,
            \json_encode($command, \JSON_THROW_ON_ERROR | \JSON_FORCE_OBJECT)
        );
    }

    #[Test]
    public function serializationAllData(): void
    {
        $command = new PublishCommand(
            data: ['bar' => 'baz'],
            channel: 'foo',
            skipHistory: true,
            tags: ['tag' => 'value'],
            base64data: 'qwerty',
        );
        self::assertJsonStringEqualsJsonString(
            <<<'JSON'
                {
                    "channel": "foo",
                    "data": {
                        "bar": "baz"
                    },
                    "skip_history": true,
                    "tags": {
                        "tag": "value"
                    },
                    "b64data": "qwerty"
                }
            JSON,
            \json_encode($command, \JSON_THROW_ON_ERROR | \JSON_FORCE_OBJECT)
        );
    }
}
