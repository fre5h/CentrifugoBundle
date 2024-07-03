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

namespace Fresh\CentrifugoBundle\Tests\Token;

use Fresh\CentrifugoBundle\Token\AbstractJwtPayload;
use Fresh\CentrifugoBundle\Token\JwtPayloadForPrivateChannel;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * JwtPayloadForPrivateChannelTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class JwtPayloadForPrivateChannelTest extends TestCase
{
    #[Test]
    public function constructor(): void
    {
        $jwtPayloadForPrivateChannel = new JwtPayloadForPrivateChannel(
            'spiderman',
            'avengers',
            [
                'name' => 'Peter Parker',
                'email' => 'spiderman@marvel.com',
            ],
            [
                'foo' => 'bar',
            ],
            123,
            'test',
            true,
        );

        self::assertInstanceOf(AbstractJwtPayload::class, $jwtPayloadForPrivateChannel);
        self::assertSame('spiderman', $jwtPayloadForPrivateChannel->getClient());
        self::assertSame('avengers', $jwtPayloadForPrivateChannel->getChannel());
        self::assertSame(
            [
                'name' => 'Peter Parker',
                'email' => 'spiderman@marvel.com',
            ],
            $jwtPayloadForPrivateChannel->getInfo(),
        );
        self::assertSame(
            [
                'foo' => 'bar',
            ],
            $jwtPayloadForPrivateChannel->getMeta(),
        );
        self::assertSame(123, $jwtPayloadForPrivateChannel->getExpirationTime());
        self::assertSame('test', $jwtPayloadForPrivateChannel->getBase64Info());
        self::assertTrue($jwtPayloadForPrivateChannel->isEto());
    }

    #[Test]
    public function getPayloadDataWithAllClaims(): void
    {
        $jwtPayloadForPrivateChannel = new JwtPayloadForPrivateChannel(
            'spiderman',
            'avengers',
            [
                'name' => 'Peter Parker',
                'email' => 'spiderman@marvel.com',
            ],
            [
                'foo' => 'bar',
            ],
            123,
            'test',
            true,
        );

        self::assertEquals(
            [
                'client' => 'spiderman',
                'channel' => 'avengers',
                'info' => [
                    'name' => 'Peter Parker',
                    'email' => 'spiderman@marvel.com',
                ],
                'meta' => [
                    'foo' => 'bar',
                ],
                'exp' => 123,
                'b64info' => 'test',
                'eto' => true,
            ],
            $jwtPayloadForPrivateChannel->getPayloadData(),
        );
    }

    #[Test]
    public function getPayloadDataWithOnlyRequiredClaims(): void
    {
        $jwtPayloadForPrivateChannel = new JwtPayloadForPrivateChannel(
            'spiderman',
            'avengers',
        );

        self::assertEquals(
            [
                'client' => 'spiderman',
                'channel' => 'avengers',
            ],
            $jwtPayloadForPrivateChannel->getPayloadData(),
        );
    }
}
