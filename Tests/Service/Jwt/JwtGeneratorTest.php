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
use Fresh\CentrifugoBundle\Token\JwtPayloadForPrivateChannel;
use PHPUnit\Framework\TestCase;

/**
 * JwtGeneratorTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class JwtGeneratorTest extends TestCase
{
    /** @var JwtGenerator */
    private $jwtGenerator;

    protected function setUp(): void
    {
        $this->jwtGenerator = new JwtGenerator('qwerty');
    }

    protected function tearDown(): void
    {
        unset($this->jwtGenerator);
    }

    public function testGenerateTokenForAllClaims(): void
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

    public function testGenerateTokenForOnlyRequiredClaims(): void
    {
        $jwtPayload = new JwtPayload('spiderman');

        self::assertSame(
            'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJzcGlkZXJtYW4ifQ.L9EM5Iw3MKRNnEPnWiBf_CLDtSmjG5dprKx28XPBdm4',
            $this->jwtGenerator->generateToken($jwtPayload)
        );
    }

    public function testGenerateTokenForPrivateChannelForAllClaims(): void
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

    public function testGenerateTokenForPrivateChannelForOnlyRequiredClaims(): void
    {
        $jwtPayloadForPrivateChannel = new JwtPayloadForPrivateChannel('spiderman', 'avengers');

        self::assertSame(
            'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJjbGllbnQiOiJzcGlkZXJtYW4iLCJjaGFubmVsIjoiYXZlbmdlcnMifQ.x2UWWlh823m_EelPCSuoIuik0s4DuYRX9_vRhXEVaeQ',
            $this->jwtGenerator->generateToken($jwtPayloadForPrivateChannel)
        );
    }
}
