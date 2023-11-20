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

use Fresh\CentrifugoBundle\Token\JwtPayloadForChannel;
use Fresh\CentrifugoBundle\Token\JwtPayloadForChannelOverride;
use PHPUnit\Framework\TestCase;

/**
 * JwtPayloadForChannelTest.
 */
final class JwtPayloadForChannelTest extends TestCase
{
    public function testConstructor(): void
    {
        $jwtPayloadForChannel = new JwtPayloadForChannel(
            'spiderman',
            'avengers',
            [
                'name' => 'Peter Parker',
                'email' => 'spiderman@marvel.com',
            ],
            123,
            'test',
            321,
            ['audience'],
            'issuer',
            456,
            'jwtId',
            new JwtPayloadForChannelOverride(true, false, true, false, true)
        );

        self::assertInstanceOf(JwtPayloadForChannel::class, $jwtPayloadForChannel);
        self::assertSame('spiderman', $jwtPayloadForChannel->getSubject());
        self::assertSame('avengers', $jwtPayloadForChannel->getChannel());
        self::assertSame(
            [
                'name' => 'Peter Parker',
                'email' => 'spiderman@marvel.com',
            ],
            $jwtPayloadForChannel->getInfo()
        );
        self::assertSame(123, $jwtPayloadForChannel->getExpirationTime());
        self::assertSame('test', $jwtPayloadForChannel->getBase64Info());
        self::assertSame(321, $jwtPayloadForChannel->getSubscriptionExpirationTime());
        self::assertSame(['audience'], $jwtPayloadForChannel->getAudiences());
        self::assertSame('issuer', $jwtPayloadForChannel->getIssuer());
        self::assertSame(456, $jwtPayloadForChannel->getIssuedAt());
        self::assertSame('jwtId', $jwtPayloadForChannel->getJwtId());
        self::assertInstanceOf(JwtPayloadForChannelOverride::class, $jwtPayloadForChannel->getOverride());
        self::assertTrue($jwtPayloadForChannel->getOverride()->getPresence());
        self::assertFalse($jwtPayloadForChannel->getOverride()->getJoinLeave());
        self::assertTrue($jwtPayloadForChannel->getOverride()->getForcePushJoinLeave());
        self::assertFalse($jwtPayloadForChannel->getOverride()->getForceRecovery());
        self::assertTrue($jwtPayloadForChannel->getOverride()->getForcePosting());
    }

    public function testGetPayloadDataWithAllClaims(): void
    {
        $jwtPayloadForChannel = new JwtPayloadForChannel(
            'spiderman',
            'avengers',
            [
                'name' => 'Peter Parker',
                'email' => 'spiderman@marvel.com',
            ],
            123,
            'test',
            321,
            ['audience'],
            'issuer',
            456,
            'jwtId',
            new JwtPayloadForChannelOverride(false, true, false, true, false)
        );

        self::assertEquals(
            [
                'sub' => 'spiderman',
                'channel' => 'avengers',
                'info' => [
                    'name' => 'Peter Parker',
                    'email' => 'spiderman@marvel.com',
                ],
                'b64info' => 'test',
                'exp' => 123,
                'expire_at' => 321,
                'aud' => ['audience'],
                'iss' => 'issuer',
                'iat' => 456,
                'jti' => 'jwtId',
                'override' => [
                    'presence' => [
                        'value' => false,
                    ],
                    'join_leave' => [
                        'value' => true,
                    ],
                    'force_push_join_leave' => [
                        'value' => false,
                    ],
                    'force_recovery' => [
                        'value' => true,
                    ],
                    'force_posting' => [
                        'value' => false,
                    ],
                ],
            ],
            $jwtPayloadForChannel->getPayloadData()
        );
    }

    public function testGetPayloadDataWithOnlyRequiredClaims(): void
    {
        $jwtPayloadForChannel = new JwtPayloadForChannel(
            'spiderman',
            'avengers',
        );

        self::assertEquals(
            [
                'sub' => 'spiderman',
                'channel' => 'avengers',
            ],
            $jwtPayloadForChannel->getPayloadData()
        );
    }
}
