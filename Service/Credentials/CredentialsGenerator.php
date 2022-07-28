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

namespace Fresh\CentrifugoBundle\Service\Credentials;

use Fresh\CentrifugoBundle\Service\Jwt\JwtGenerator;
use Fresh\CentrifugoBundle\Token\JwtPayload;
use Fresh\CentrifugoBundle\Token\JwtPayloadForPrivateChannel;
use Fresh\CentrifugoBundle\User\CentrifugoUserInterface;
use Fresh\DateTime\DateTimeHelper;

/**
 * CredentialsGenerator.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
class CredentialsGenerator
{
    /**
     * @param JwtGenerator   $jwtGenerator
     * @param DateTimeHelper $dateTimeHelper
     * @param int|null       $centrifugoJwtTtl
     */
    public function __construct(private readonly JwtGenerator $jwtGenerator, private readonly DateTimeHelper $dateTimeHelper, private readonly ?int $centrifugoJwtTtl = null)
    {
    }

    /**
     * @param CentrifugoUserInterface $user
     * @param string|null             $base64info
     * @param array                   $channels
     *
     * @return string
     */
    public function generateJwtTokenForUser(CentrifugoUserInterface $user, ?string $base64info = null, array $channels = []): string
    {
        $jwtPayload = new JwtPayload(
            $user->getCentrifugoSubject(),
            $user->getCentrifugoUserInfo(),
            $this->getExpirationTime(),
            $base64info,
            $channels
        );

        return $this->jwtGenerator->generateToken($jwtPayload);
    }

    /**
     * @param string|null $base64info
     * @param array       $channels
     *
     * @return string
     */
    public function generateJwtTokenForAnonymous(?string $base64info = null, array $channels = []): string
    {
        $jwtPayload = new JwtPayload(
            '',
            [],
            $this->getExpirationTime(),
            $base64info,
            $channels
        );

        return $this->jwtGenerator->generateToken($jwtPayload);
    }

    /**
     * @param string      $client
     * @param string      $channel
     * @param string|null $base64info
     * @param bool|null   $eto
     *
     * @return string
     */
    public function generateJwtTokenForPrivateChannel(string $client, string $channel, ?string $base64info = null, ?bool $eto = null): string
    {
        $jwtPayload = new JwtPayloadForPrivateChannel(
            $client,
            $channel,
            [],
            $this->getExpirationTime(),
            $base64info,
            $eto
        );

        return $this->jwtGenerator->generateToken($jwtPayload);
    }

    /**
     * @return int|null
     */
    private function getExpirationTime(): ?int
    {
        $expireAt = null;

        if (null !== $this->centrifugoJwtTtl) {
            $now = $this->dateTimeHelper->getCurrentDatetime();
            $now->add(new \DateInterval("PT{$this->centrifugoJwtTtl}S"));
            $expireAt = $now->getTimestamp();
        }

        return $expireAt;
    }
}
