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
use Fresh\CentrifugoBundle\Token\JwtPayload;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * JwtPayloadTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class JwtPayloadTest extends TestCase
{
    #[Test]
    public function constructor(): void
    {
        $jwtPayload = new JwtPayload(
            'spiderman',
            [
                'name' => 'Peter Parker',
                'email' => 'spiderman@marvel.com',
            ],
            [
                'foo' => 'bar',
            ],
            123,
            'test',
            ['avengers']
        );

        self::assertInstanceOf(AbstractJwtPayload::class, $jwtPayload);
        self::assertSame('spiderman', $jwtPayload->getSubject());
        self::assertSame(
            [
                'name' => 'Peter Parker',
                'email' => 'spiderman@marvel.com',
            ],
            $jwtPayload->getInfo()
        );
        self::assertSame(
            [
                'foo' => 'bar',
            ],
            $jwtPayload->getMeta()
        );
        self::assertSame(123, $jwtPayload->getExpirationTime());
        self::assertSame('test', $jwtPayload->getBase64Info());
        self::assertSame(['avengers'], $jwtPayload->getChannels());
    }

    #[Test]
    public function getPayloadDataWithAllClaims(): void
    {
        $jwtPayload = new JwtPayload(
            'spiderman',
            [
                'name' => 'Peter Parker',
                'email' => 'spiderman@marvel.com',
            ],
            [
                'foo' => 'bar',
            ],
            123,
            'test',
            ['avengers']
        );

        self::assertEquals(
            [
                'sub' => 'spiderman',
                'info' => [
                    'name' => 'Peter Parker',
                    'email' => 'spiderman@marvel.com',
                ],
                'meta' => [
                    'foo' => 'bar',
                ],
                'exp' => 123,
                'b64info' => 'test',
                'channels' => ['avengers'],
            ],
            $jwtPayload->getPayloadData()
        );
    }

    #[Test]
    public function getPayloadDataWithOnlyRequiredClaims(): void
    {
        $jwtPayload = new JwtPayload(
            'spiderman'
        );

        self::assertEquals(
            [
                'sub' => 'spiderman',
            ],
            $jwtPayload->getPayloadData()
        );
    }
}
