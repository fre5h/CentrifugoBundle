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
use Fresh\CentrifugoBundle\Token\JwtSubscriptionPayload;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * JwtSubscriptionPayload.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class JwtSubscriptionPayloadTest extends TestCase
{
    #[Test]
    public function constructor(): void
    {
        $jwtPayload = new JwtSubscriptionPayload(
            'spiderman',
            'channel',
            [
                'name' => 'Peter Parker',
                'email' => 'spiderman@marvel.com',
            ],
            [
                'foo' => 'bar',
            ],
            123,
            'test',
        );

        $this->assertInstanceOf(AbstractJwtPayload::class, $jwtPayload);
        $this->assertSame('spiderman', $jwtPayload->getSubject());
        $this->assertSame('channel', $jwtPayload->getChannel());
        $this->assertSame(
            [
                'name' => 'Peter Parker',
                'email' => 'spiderman@marvel.com',
            ],
            $jwtPayload->getInfo(),
        );
        $this->assertSame(
            [
                'foo' => 'bar',
            ],
            $jwtPayload->getMeta(),
        );
        $this->assertSame(123, $jwtPayload->getExpirationTime());
        $this->assertSame('test', $jwtPayload->getBase64Info());
    }

    #[Test]
    public function getPayloadDataWithAllClaims(): void
    {
        $jwtPayload = new JwtSubscriptionPayload(
            'spiderman',
            'channel',
            [
                'name' => 'Peter Parker',
                'email' => 'spiderman@marvel.com',
            ],
            [
                'foo' => 'bar',
            ],
            123,
            'test',
        );

        $this->assertEquals(
            [
                'sub' => 'spiderman',
                'channel' => 'channel',
                'info' => [
                    'name' => 'Peter Parker',
                    'email' => 'spiderman@marvel.com',
                ],
                'meta' => [
                    'foo' => 'bar',
                ],
                'exp' => 123,
                'b64info' => 'test',
            ],
            $jwtPayload->getPayloadData(),
        );
    }

    #[Test]
    public function getPayloadDataWithOnlyRequiredClaims(): void
    {
        $jwtPayload = new JwtSubscriptionPayload(
            'spiderman',
            'channel',
        );

        $this->assertEquals(
            [
                'sub' => 'spiderman',
                'channel' => 'channel',
            ],
            $jwtPayload->getPayloadData(),
        );
    }
}
