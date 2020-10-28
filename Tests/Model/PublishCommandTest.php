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
use PHPUnit\Framework\TestCase;

/**
 * PublishCommandTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class PublishCommandTest extends TestCase
{
    /** @var PublishCommand */
    private $command;

    protected function setUp(): void
    {
        $this->command = new PublishCommand(['bar' => 'baz'], 'foo');
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
        self::assertEquals(Method::PUBLISH, $this->command->getMethod());
        self::assertEquals(['channel' => 'foo', 'data' => ['bar' => 'baz']], $this->command->getParams());
        self::assertEquals(['foo'], $this->command->getChannels());
    }

    public function testSerialization(): void
    {
        self::assertJsonStringEqualsJsonString(
            <<<'JSON'
                {
                    "method": "publish",
                    "params": {
                        "channel": "foo",
                        "data": {
                            "bar": "baz"
                        }
                    }
                }
            JSON,
            \json_encode($this->command, JSON_THROW_ON_ERROR)
        );
    }
}
