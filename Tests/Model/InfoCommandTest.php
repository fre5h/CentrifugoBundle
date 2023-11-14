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
use Fresh\CentrifugoBundle\Model\InfoCommand;
use Fresh\CentrifugoBundle\Model\Method;
use Fresh\CentrifugoBundle\Model\SerializableCommandInterface;
use PHPUnit\Framework\TestCase;

/**
 * InfoCommandTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class InfoCommandTest extends TestCase
{
    public function testInterfaces(): void
    {
        $command = new InfoCommand();
        self::assertInstanceOf(SerializableCommandInterface::class, $command);
        self::assertInstanceOf(CommandInterface::class, $command);
    }

    public function testConstructor(): void
    {
        $command = new InfoCommand();
        self::assertEquals(Method::INFO, $command->getMethod());
        self::assertEquals([], $command->getParams());
        self::assertEquals([], $command->getChannels());
    }

    public function testSerialization(): void
    {
        $command = new InfoCommand();
        self::assertJsonStringEqualsJsonString(
            <<<'JSON'
                {
                    "method": "info",
                    "params": {}
                }
            JSON,
            \json_encode($command, \JSON_THROW_ON_ERROR | \JSON_FORCE_OBJECT)
        );
    }
}
