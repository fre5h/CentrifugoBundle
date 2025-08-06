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
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * JwtPayloadForChannelTest.
 */
final class JwtPayloadForChannelTest extends TestCase
{
    #[Test]
    public function constructor(): void
    {
        $jwtPayloadForChannel = new JwtPayloadForChannel(
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
            321,
            ['audience'],
            'issuer',
            456,
            'jwtId',
            new JwtPayloadForChannelOverride(true, false, true, false, true),
        );

        $this->assertInstanceOf(JwtPayloadForChannel::class, $jwtPayloadForChannel);
        $this->assertSame('spiderman', $jwtPayloadForChannel->getSubject());
        $this->assertSame('avengers', $jwtPayloadForChannel->getChannel());
        $this->assertSame(
            [
                'name' => 'Peter Parker',
                'email' => 'spiderman@marvel.com',
            ],
            $jwtPayloadForChannel->getInfo(),
        );
        $this->assertSame(
            [
                'foo' => 'bar',
            ],
            $jwtPayloadForChannel->getMeta(),
        );
        $this->assertSame(123, $jwtPayloadForChannel->getExpirationTime());
        $this->assertSame('test', $jwtPayloadForChannel->getBase64Info());
        $this->assertSame(321, $jwtPayloadForChannel->getSubscriptionExpirationTime());
        $this->assertSame(['audience'], $jwtPayloadForChannel->getAudiences());
        $this->assertSame('issuer', $jwtPayloadForChannel->getIssuer());
        $this->assertSame(456, $jwtPayloadForChannel->getIssuedAt());
        $this->assertSame('jwtId', $jwtPayloadForChannel->getJwtId());
        $this->assertInstanceOf(JwtPayloadForChannelOverride::class, $jwtPayloadForChannel->getOverride());
        $this->assertTrue($jwtPayloadForChannel->getOverride()->getPresence());
        $this->assertFalse($jwtPayloadForChannel->getOverride()->getJoinLeave());
        $this->assertTrue($jwtPayloadForChannel->getOverride()->getForcePushJoinLeave());
        $this->assertFalse($jwtPayloadForChannel->getOverride()->getForceRecovery());
        $this->assertTrue($jwtPayloadForChannel->getOverride()->getForcePosting());
    }

    #[Test]
    public function getPayloadDataWithAllClaims(): void
    {
        $jwtPayloadForChannel = new JwtPayloadForChannel(
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
            321,
            ['audience'],
            'issuer',
            456,
            'jwtId',
            new JwtPayloadForChannelOverride(false, true, false, true, false),
        );

        $this->assertEquals(
            [
                'sub' => 'spiderman',
                'channel' => 'avengers',
                'info' => [
                    'name' => 'Peter Parker',
                    'email' => 'spiderman@marvel.com',
                ],
                'meta' => [
                    'foo' => 'bar',
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
            $jwtPayloadForChannel->getPayloadData(),
        );
    }

    #[Test]
    public function getPayloadDataWithOnlyRequiredClaims(): void
    {
        $jwtPayloadForChannel = new JwtPayloadForChannel(
            'spiderman',
            'avengers',
        );

        $this->assertEquals(
            [
                'sub' => 'spiderman',
                'channel' => 'avengers',
            ],
            $jwtPayloadForChannel->getPayloadData(),
        );
    }
}
