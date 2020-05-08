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
use PHPUnit\Framework\TestCase;

/**
 * JwtPayloadTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class JwtPayloadTest extends TestCase
{
    public function testConstructor(): void
    {
        $jwtPayload = new JwtPayload(
            'spiderman',
            [
                'name' => 'Peter Parker',
                'email' => 'spiderman@marvel.com',
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
        self::assertSame(123, $jwtPayload->getExpirationTime());
        self::assertSame('test', $jwtPayload->getBase64Info());
        self::assertSame(['avengers'], $jwtPayload->getChannels());
    }

    public function testGetPayloadDataWithAllClaims(): void
    {
        $jwtPayload = new JwtPayload(
            'spiderman',
            [
                'name' => 'Peter Parker',
                'email' => 'spiderman@marvel.com',
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
                'exp' => 123,
                'b64info' => 'test',
                'channels' => ['avengers'],
            ],
            $jwtPayload->getPayloadData()
        );
    }

    public function testGetPayloadDataWithOnlyRequiredClaims(): void
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
