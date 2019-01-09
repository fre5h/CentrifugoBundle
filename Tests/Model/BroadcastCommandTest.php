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

use Fresh\CentrifugoBundle\Model\BroadcastCommand;
use Fresh\CentrifugoBundle\Model\CommandInterface;
use Fresh\CentrifugoBundle\Model\Method;
use Fresh\CentrifugoBundle\Model\SerializableCommandInterface;
use PHPUnit\Framework\TestCase;

/**
 * BroadcastCommandTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class BroadcastCommandTest extends TestCase
{
    /** @var BroadcastCommand */
    private $command;

    protected function setUp(): void
    {
        $this->command = new BroadcastCommand(['baz' => 'qux'], ['foo', 'bar']);
    }

    protected function tearDown(): void
    {
        unset($this->command);
    }

    public function testInterfaces(): void
    {
        self::assertInstanceOf(SerializableCommandInterface::class, $this->command);
        self::assertInstanceOf(CommandInterface::class, $this->command);
    }

    public function testGetters(): void
    {
        self::assertEquals(Method::BROADCAST, $this->command->getMethod());
        self::assertEquals(['channels' => ['foo', 'bar'], 'data' => ['baz' => 'qux']], $this->command->getParams());
        self::assertEquals(['foo', 'bar'], $this->command->getChannels());
    }

    public function testSerialization(): void
    {
        self::assertJsonStringEqualsJsonString(
            <<<'JSON'
                {
                    "method": "broadcast",
                    "params": {
                        "channels": ["foo", "bar"],
                        "data": {
                            "baz": "qux"
                        }
                    }
                }
            JSON,
            \json_encode($this->command, \JSON_THROW_ON_ERROR)
        );
    }
}
