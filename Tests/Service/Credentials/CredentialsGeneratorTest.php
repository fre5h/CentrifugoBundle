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
use Fresh\CentrifugoBundle\Token\JwtPayloadForPrivateChannel;
use Fresh\CentrifugoBundle\User\CentrifugoUserInterface;
use Fresh\DateTime\DateTimeHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * CredentialsGeneratorTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class CredentialsGeneratorTest extends TestCase
{
    /** @var JwtGenerator|MockObject */
    private $jwtGenerator;

    /** @var DateTimeHelper|MockObject */
    private $dateTimeHelper;

    /** @var CredentialsGenerator */
    private $credentialsGenerator;

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

    public function testGenerateJwtTokenForAnonymous(): void
    {
        $this->dateTimeHelper
            ->expects(self::once())
            ->method('getCurrentDatetime')
            ->willReturn(new \DateTime('2000-01-01 00:00:00'))
        ;

        $this->jwtGenerator
            ->expects(self::once())
            ->method('generateToken')
            ->with($this->callback(static function (JwtPayload $jwtPayload) {
                return '' === $jwtPayload->getSubject()
                    && [] === $jwtPayload->getInfo()
                    && 946684810 === $jwtPayload->getExpirationTime() // 2000-01-01 00:00:10
                    && null === $jwtPayload->getBase64Info()
                    && [] === $jwtPayload->getChannels()
                ;
            }))
            ->willReturn('test1')
        ;

        self::assertEquals('test1', $this->credentialsGenerator->generateJwtTokenForAnonymous());
    }

    public function testGenerateJwtTokenForUser(): void
    {
        $this->dateTimeHelper
            ->expects(self::once())
            ->method('getCurrentDatetime')
            ->willReturn(new \DateTime('2000-02-02 00:00:00'))
        ;

        $user = $this->createMock(CentrifugoUserInterface::class);
        $user
            ->expects(self::once())
            ->method('getCentrifugoSubject')
            ->willReturn('spiderman')
        ;
        $user
            ->expects(self::once())
            ->method('getCentrifugoUserInfo')
            ->willReturn(
                [
                    'name' => 'Peter Parker',
                    'email' => 'spiderman@marvel.com',
                ]
            )
        ;

        $this->jwtGenerator
            ->expects(self::once())
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

        self::assertEquals('test2', $this->credentialsGenerator->generateJwtTokenForUser($user, 'qwerty', ['channelA']));
    }

    public function testGenerateJwtTokenForPrivateChannel(): void
    {
        $this->dateTimeHelper
            ->expects(self::once())
            ->method('getCurrentDatetime')
            ->willReturn(new \DateTime('2000-03-03 00:00:00'))
        ;

        $this->jwtGenerator
            ->expects(self::once())
            ->method('generateToken')
            ->with($this->callback(static function (JwtPayloadForPrivateChannel $jwtPayloadForPrivateChannel) {
                return 'spiderman' === $jwtPayloadForPrivateChannel->getClient()
                    && 'avengers' === $jwtPayloadForPrivateChannel->getChannel()
                    && [] === $jwtPayloadForPrivateChannel->getInfo()
                    && 952041610 === $jwtPayloadForPrivateChannel->getExpirationTime() // 2000-03-03 00:00:10
                    && null === $jwtPayloadForPrivateChannel->getBase64Info()
                    && true === $jwtPayloadForPrivateChannel->isEto()
                ;
            }))
            ->willReturn('test3')
        ;

        self::assertEquals('test3', $this->credentialsGenerator->generateJwtTokenForPrivateChannel('spiderman', 'avengers', null, true));
    }
}
