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
        $jsonWebTokenPayload = new JwtPayload(
            'spiderman',
            [
                'name' => 'Peter Parker',
                'email' => 'spiderman@marvel.com',
            ],
            123,
            'qwerty',
            ['avengers']
        );

        self::assertSame('spiderman', $jsonWebTokenPayload->getSubject());
        self::assertSame(
            [
                'name' => 'Peter Parker',
                'email' => 'spiderman@marvel.com',
            ],
            $jsonWebTokenPayload->getInfo()
        );
        self::assertSame(123, $jsonWebTokenPayload->getExpirationTime());
        self::assertSame('qwerty', $jsonWebTokenPayload->getBase64Info());
        self::assertSame(['avengers'], $jsonWebTokenPayload->getChannels());
    }
}
