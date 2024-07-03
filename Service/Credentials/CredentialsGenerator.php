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
use Fresh\CentrifugoBundle\Token\JwtPayloadForChannel;
use Fresh\CentrifugoBundle\Token\JwtPayloadForPrivateChannel;
use Fresh\CentrifugoBundle\User\CentrifugoUserInterface;
use Fresh\CentrifugoBundle\User\CentrifugoUserMetaInterface;
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
     * @param CentrifugoUserInterface|CentrifugoUserMetaInterface $user
     * @param string|null                                         $base64info
     * @param array                                               $channels
     *
     * @return string
     */
    public function generateJwtTokenForUser(CentrifugoUserInterface|CentrifugoUserMetaInterface $user, ?string $base64info = null, array $channels = []): string
    {
        $jwtPayload = new JwtPayload(
            subject: $user->getCentrifugoSubject(),
            info: $user->getCentrifugoUserInfo(),
            meta: $user instanceof CentrifugoUserMetaInterface ? $user->getCentrifugoUserMeta() : [],
            expirationTime: $this->getExpirationTime(),
            base64info: $base64info,
            channels: $channels,
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
            subject: '',
            info: [],
            meta: [],
            expirationTime: $this->getExpirationTime(),
            base64info: $base64info,
            channels: $channels
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
            client: $client,
            channel: $channel,
            info: [],
            meta: [],
            expirationTime: $this->getExpirationTime(),
            base64info: $base64info,
            eto: $eto,
        );

        return $this->jwtGenerator->generateToken($jwtPayload);
    }

    /**
     * @param CentrifugoUserInterface|CentrifugoUserMetaInterface $user
     * @param string                                              $channel
     * @param array                                               $info
     * @param string|null                                         $base64info
     *
     * @return string
     */
    public function generateJwtTokenForChannel(CentrifugoUserInterface|CentrifugoUserMetaInterface $user, string $channel, array $info = [], ?string $base64info = null): string
    {
        $jwtPayload = new JwtPayloadForChannel(
            subject: $user->getCentrifugoSubject(),
            channel: $channel,
            info: $info,
            meta: $user instanceof CentrifugoUserMetaInterface ? $user->getCentrifugoUserMeta() : [],
            expirationTime: $this->getExpirationTime(),
            base64info: $base64info,
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
            $now = $this->dateTimeHelper->getCurrentDatetimeUtc();
            $now->add(new \DateInterval("PT{$this->centrifugoJwtTtl}S"));
            $expireAt = $now->getTimestamp();
        }

        return $expireAt;
    }
}
