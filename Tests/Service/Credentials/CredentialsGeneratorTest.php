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

namespace Fresh\CentrifugoBundle\Tests\Service\Credentials;

use Fresh\CentrifugoBundle\Service\Credentials\CredentialsGenerator;
use Fresh\CentrifugoBundle\Service\Jwt\JwtGenerator;
use Fresh\CentrifugoBundle\Token\JwtPayload;
use Fresh\CentrifugoBundle\Token\JwtPayloadForChannel;
use Fresh\CentrifugoBundle\Token\JwtPayloadForPrivateChannel;
use Fresh\CentrifugoBundle\User\CentrifugoUserInterface;
use Fresh\CentrifugoBundle\User\CentrifugoUserMetaInterface;
use Fresh\DateTime\DateTimeHelper;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * CredentialsGeneratorTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class CredentialsGeneratorTest extends TestCase
{
    private JwtGenerator|MockObject $jwtGenerator;
    private DateTimeHelper|MockObject $dateTimeHelper;
    private CredentialsGenerator $credentialsGenerator;

    protected function setUp(): void
    {
        $this->jwtGenerator = $this->createMock(JwtGenerator::class);
        $this->dateTimeHelper = $this->createMock(DateTimeHelper::class);
        $this->credentialsGenerator = new CredentialsGenerator($this->jwtGenerator, $this->dateTimeHelper, 10);
    }

    protected function tearDown(): void
    {
        unset(
            $this->jwtGenerator,
            $this->dateTimeHelper,
            $this->credentialsGenerator,
        );
    }

    #[Test]
    public function generateJwtTokenForAnonymous(): void
    {
        $this->dateTimeHelper
            ->expects($this->once())
            ->method('getCurrentDatetimeUtc')
            ->willReturn(new \DateTime('2000-01-01 00:00:00', new \DateTimeZone('UTC')))
        ;

        $this->jwtGenerator
            ->expects($this->once())
            ->method('generateToken')
            ->with($this->callback(static function (JwtPayload $jwtPayload) {
                return '' === $jwtPayload->getSubject()
                    && [] === $jwtPayload->getInfo()
                    && [] === $jwtPayload->getMeta()
                    && 946684810 === $jwtPayload->getExpirationTime() // 2000-01-01 00:00:10
                    && null === $jwtPayload->getBase64Info()
                    && [] === $jwtPayload->getChannels()
                ;
            }))
            ->willReturn('test1')
        ;

        $this->assertEquals('test1', $this->credentialsGenerator->generateJwtTokenForAnonymous());
    }

    #[Test]
    public function generateJwtTokenForUser(): void
    {
        $this->dateTimeHelper
            ->expects($this->once())
            ->method('getCurrentDatetimeUtc')
            ->willReturn(new \DateTime('2000-02-02 00:00:00', new \DateTimeZone('UTC')))
        ;

        $user = $this->createMock(CentrifugoUserInterface::class);
        $user
            ->expects($this->once())
            ->method('getCentrifugoSubject')
            ->willReturn('spiderman')
        ;
        $user
            ->expects($this->once())
            ->method('getCentrifugoUserInfo')
            ->willReturn(
                [
                    'name' => 'Peter Parker',
                    'email' => 'spiderman@marvel.com',
                ],
            )
        ;

        $this->jwtGenerator
            ->expects($this->once())
            ->method('generateToken')
            ->with($this->callback(static function (JwtPayload $jwtPayload) {
                return 'spiderman' === $jwtPayload->getSubject()
                    && ['name' => 'Peter Parker', 'email' => 'spiderman@marvel.com'] === $jwtPayload->getInfo()
                    && 949449610 === $jwtPayload->getExpirationTime() // 2000-02-02 00:00:10
                    && 'qwerty' === $jwtPayload->getBase64Info()
                    && ['channelA'] === $jwtPayload->getChannels()
                ;
            }))
            ->willReturn('test2')
        ;

        $this->assertEquals('test2', $this->credentialsGenerator->generateJwtTokenForUser($user, 'qwerty', ['channelA']));
    }

    #[Test]
    public function generateJwtTokenForUserWithMeta(): void
    {
        $this->dateTimeHelper
            ->expects($this->once())
            ->method('getCurrentDatetimeUtc')
            ->willReturn(new \DateTime('2000-02-02 00:00:00', new \DateTimeZone('UTC')))
        ;

        $user = $this->createMock(CentrifugoUserMetaInterface::class);
        $user
            ->expects($this->once())
            ->method('getCentrifugoSubject')
            ->willReturn('spiderman')
        ;
        $user
            ->expects($this->once())
            ->method('getCentrifugoUserInfo')
            ->willReturn(
                [
                    'name' => 'Peter Parker',
                    'email' => 'spiderman@marvel.com',
                ]
            )
        ;
        $user
            ->expects($this->once())
            ->method('getCentrifugoUserMeta')
            ->willReturn(
                [
                    'foo' => 'bar',
                ]
            )
        ;

        $this->jwtGenerator
            ->expects($this->once())
            ->method('generateToken')
            ->with($this->callback(static function (JwtPayload $jwtPayload) {
                return 'spiderman' === $jwtPayload->getSubject()
                    && ['name' => 'Peter Parker', 'email' => 'spiderman@marvel.com'] === $jwtPayload->getInfo()
                    && ['foo' => 'bar'] === $jwtPayload->getMeta()
                    && 949449610 === $jwtPayload->getExpirationTime() // 2000-02-02 00:00:10
                    && 'qwerty' === $jwtPayload->getBase64Info()
                    && ['channelA'] === $jwtPayload->getChannels()
                ;
            }))
            ->willReturn('test2')
        ;

        $this->assertEquals('test2', $this->credentialsGenerator->generateJwtTokenForUser($user, 'qwerty', ['channelA']));
    }

    #[Test]
    public function generateJwtTokenForPrivateChannel(): void
    {
        $this->dateTimeHelper
            ->expects($this->once())
            ->method('getCurrentDatetimeUtc')
            ->willReturn(new \DateTime('2000-03-03 00:00:00', new \DateTimeZone('UTC')))
        ;

        $this->jwtGenerator
            ->expects($this->once())
            ->method('generateToken')
            ->with($this->callback(static function (JwtPayloadForPrivateChannel $jwtPayloadForPrivateChannel) {
                return 'spiderman' === $jwtPayloadForPrivateChannel->getClient()
                    && 'avengers' === $jwtPayloadForPrivateChannel->getChannel()
                    && [] === $jwtPayloadForPrivateChannel->getInfo()
                    && [] === $jwtPayloadForPrivateChannel->getMeta()
                    && 952041610 === $jwtPayloadForPrivateChannel->getExpirationTime() // 2000-03-03 00:00:10
                    && null === $jwtPayloadForPrivateChannel->getBase64Info()
                    && true === $jwtPayloadForPrivateChannel->isEto()
                ;
            }))
            ->willReturn('test3')
        ;

        $this->assertEquals('test3', $this->credentialsGenerator->generateJwtTokenForPrivateChannel('spiderman', 'avengers', null, true));
    }

    #[Test]
    public function generateJwtTokenForChannel(): void
    {
        $this->dateTimeHelper
            ->expects($this->once())
            ->method('getCurrentDatetimeUtc')
            ->willReturn(new \DateTime('2000-03-03 00:00:00', new \DateTimeZone('UTC')))
        ;

        $user = $this->createMock(CentrifugoUserInterface::class);
        $user
            ->expects($this->once())
            ->method('getCentrifugoSubject')
            ->willReturn('spiderman')
        ;

        $this->jwtGenerator
            ->expects($this->once())
            ->method('generateToken')
            ->with($this->callback(static function (JwtPayloadForChannel $jwtPayloadForChannel) {
                return 'spiderman' === $jwtPayloadForChannel->getSubject()
                    && 'avengers' === $jwtPayloadForChannel->getChannel()
                    && [] === $jwtPayloadForChannel->getInfo()
                    && [] === $jwtPayloadForChannel->getMeta()
                    && 952041610 === $jwtPayloadForChannel->getExpirationTime() // 2000-03-03 00:00:10
                    && null === $jwtPayloadForChannel->getBase64Info()
                    && null === $jwtPayloadForChannel->getSubscriptionExpirationTime()
                    && [] === $jwtPayloadForChannel->getAudiences()
                    && null === $jwtPayloadForChannel->getIssuer()
                    && null === $jwtPayloadForChannel->getIssuedAt()
                    && null === $jwtPayloadForChannel->getJwtId()
                    && null === $jwtPayloadForChannel->getOverride()
                ;
            }))
            ->willReturn('test4')
        ;

        $this->assertEquals('test4', $this->credentialsGenerator->generateJwtTokenForChannel($user, 'avengers'));
    }
}
