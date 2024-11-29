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
            ['avengers'],
        );

        $this->assertInstanceOf(AbstractJwtPayload::class, $jwtPayload);
        $this->assertSame('spiderman', $jwtPayload->getSubject());
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
        $this->assertSame(['avengers'], $jwtPayload->getChannels());
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
            ['avengers'],
        );

        $this->assertEquals(
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
            $jwtPayload->getPayloadData(),
        );
    }

    #[Test]
    public function getPayloadDataWithOnlyRequiredClaims(): void
    {
        $jwtPayload = new JwtPayload(
            'spiderman'
        );

        $this->assertEquals(
            [
                'sub' => 'spiderman',
            ],
            $jwtPayload->getPayloadData(),
        );
    }
}
