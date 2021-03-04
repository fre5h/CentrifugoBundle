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
use PHPUnit\Framework\TestCase;

/**
 * UnsubscribeCommandTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class UnsubscribeCommandTest extends TestCase
{
    private UnsubscribeCommand $command;

    protected function setUp(): void
    {
        $this->command = new UnsubscribeCommand('bar', 'foo');
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
        self::assertEquals(Method::UNSUBSCRIBE, $this->command->getMethod());
        self::assertEquals(['channel' => 'foo', 'user' => 'bar'], $this->command->getParams());
        self::assertEquals(['foo'], $this->command->getChannels());
    }

    public function testSerialization(): void
    {
        self::assertJsonStringEqualsJsonString(
            <<<'JSON'
                {
                    "method": "unsubscribe",
                    "params": {
                        "channel": "foo",
                        "user": "bar"
                    }
                }
            JSON,
            \json_encode($this->command, JSON_THROW_ON_ERROR)
        );
    }
}
