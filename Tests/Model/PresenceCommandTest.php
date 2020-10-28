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
use PHPUnit\Framework\TestCase;

/**
 * PresenceCommandTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class PresenceCommandTest extends TestCase
{
    /** @var PresenceCommand */
    private $command;

    protected function setUp(): void
    {
        $this->command = new PresenceCommand('foo');
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
        self::assertEquals(Method::PRESENCE, $this->command->getMethod());
        self::assertEquals(['channel' => 'foo'], $this->command->getParams());
        self::assertEquals(['foo'], $this->command->getChannels());
    }

    public function testSerialization(): void
    {
        self::assertJsonStringEqualsJsonString(
            <<<'JSON'
                {
                    "method": "presence",
                    "params": {
                        "channel": "foo"
                    }
                }
            JSON,
            \json_encode($this->command, JSON_THROW_ON_ERROR)
        );
    }
}
