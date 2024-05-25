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

namespace Fresh\CentrifugoBundle\Tests\Service\Jwt;

use Fresh\CentrifugoBundle\Service\Jwt\JwtGenerator;
use Fresh\CentrifugoBundle\Token\JwtPayload;
use Fresh\CentrifugoBundle\Token\JwtPayloadForChannel;
use Fresh\CentrifugoBundle\Token\JwtPayloadForChannelOverride;
use Fresh\CentrifugoBundle\Token\JwtPayloadForPrivateChannel;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * JwtGeneratorTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class JwtGeneratorTest extends TestCase
{
    private JwtGenerator $jwtGenerator;

    protected function setUp(): void
    {
        $this->jwtGenerator = new JwtGenerator('qwerty');
    }

    protected function tearDown(): void
    {
        unset($this->jwtGenerator);
    }

    #[Test]
    public function generateTokenForAllClaims(): void
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
            'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJzcGlkZXJtYW4iLCJleHAiOjEyMywiaW5mbyI6eyJuYW1lIjoiUGV0ZXIgUGFya2VyIiwiZW1haWwiOiJzcGlkZXJtYW5AbWFydmVsLmNvbSJ9LCJiNjRpbmZvIjoidGVzdCIsImNoYW5uZWxzIjpbImF2ZW5nZXJzIl19.PuNr9qJIj6UCcFK3ZKCHMdUyS6Rg6dcinvJ8rZQX7uM',
            $this->jwtGenerator->generateToken($jwtPayload)
        );
    }

    #[Test]
    public function generateTokenForOnlyRequiredClaims(): void
    {
        $jwtPayload = new JwtPayload('spiderman');

        self::assertSame(
            'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJzcGlkZXJtYW4ifQ.L9EM5Iw3MKRNnEPnWiBf_CLDtSmjG5dprKx28XPBdm4',
            $this->jwtGenerator->generateToken($jwtPayload)
        );
    }

    #[Test]
    public function generateTokenForPrivateChannelForAllClaims(): void
    {
        $jwtPayloadForPrivateChannel = new JwtPayloadForPrivateChannel(
            'spiderman',
            'avengers',
            [
                'name' => 'Peter Parker',
                'email' => 'spiderman@marvel.com',
            ],
            123,
            'test',
            true
        );

        self::assertEquals(
            'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJjbGllbnQiOiJzcGlkZXJtYW4iLCJjaGFubmVsIjoiYXZlbmdlcnMiLCJleHAiOjEyMywiaW5mbyI6eyJuYW1lIjoiUGV0ZXIgUGFya2VyIiwiZW1haWwiOiJzcGlkZXJtYW5AbWFydmVsLmNvbSJ9LCJiNjRpbmZvIjoidGVzdCIsImV0byI6dHJ1ZX0.RV4XpHQKRu9_6yFUHywvLbGynn2bEVTJvPoPGDnKpwk',
            $this->jwtGenerator->generateToken($jwtPayloadForPrivateChannel)
        );
    }

    #[Test]
    public function generateTokenForPrivateChannelForOnlyRequiredClaims(): void
    {
        $jwtPayloadForPrivateChannel = new JwtPayloadForPrivateChannel('spiderman', 'avengers');

        self::assertSame(
            'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJjbGllbnQiOiJzcGlkZXJtYW4iLCJjaGFubmVsIjoiYXZlbmdlcnMifQ.x2UWWlh823m_EelPCSuoIuik0s4DuYRX9_vRhXEVaeQ',
            $this->jwtGenerator->generateToken($jwtPayloadForPrivateChannel)
        );
    }

    #[Test]
    public function generateTokenForChannelForAllClaims(): void
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

        self::assertEquals(
            'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJzcGlkZXJtYW4iLCJjaGFubmVsIjoiYXZlbmdlcnMiLCJpbmZvIjp7Im5hbWUiOiJQZXRlciBQYXJrZXIiLCJlbWFpbCI6InNwaWRlcm1hbkBtYXJ2ZWwuY29tIn0sImI2NGluZm8iOiJ0ZXN0IiwiZXhwIjoxMjMsImV4cGlyZV9hdCI6MzIxLCJhdWQiOlsiYXVkaWVuY2UiXSwiaXNzIjoiaXNzdWVyIiwiaWF0Ijo0NTYsImp0aSI6Imp3dElkIiwib3ZlcnJpZGUiOnsicHJlc2VuY2UiOnsidmFsdWUiOnRydWV9LCJqb2luX2xlYXZlIjp7InZhbHVlIjpmYWxzZX0sImZvcmNlX3B1c2hfam9pbl9sZWF2ZSI6eyJ2YWx1ZSI6dHJ1ZX0sImZvcmNlX3JlY292ZXJ5Ijp7InZhbHVlIjpmYWxzZX0sImZvcmNlX3Bvc3RpbmciOnsidmFsdWUiOnRydWV9fX0.ZOq57j6pESNCZtQmIRj36cbpKYjWPWoReekLv7ZChQE',
            $this->jwtGenerator->generateToken($jwtPayloadForChannel)
        );
    }

    #[Test]
    public function generateTokenForChannelForOnlyRequiredClaims(): void
    {
        $jwtPayloadForChannel = new JwtPayloadForChannel('spiderman', 'avengers');

        self::assertSame(
            'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJzcGlkZXJtYW4iLCJjaGFubmVsIjoiYXZlbmdlcnMifQ.OYI-kcfDwuE-V06M-jkIX1-Rvdna1l9PXdMkmc_BTGY',
            $this->jwtGenerator->generateToken($jwtPayloadForChannel)
        );
    }
}
