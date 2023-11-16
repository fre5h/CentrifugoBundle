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

namespace Fresh\CentrifugoBundle\Token;

use JetBrains\PhpStorm\ArrayShape;

/**
 * JwtPayloadForChannel.
 *
 * @see https://centrifugal.dev/docs/server/channel_token_auth#subscription-jwt-claims
 */
final class JwtPayloadForChannel extends AbstractJwtPayload
{
    /**
     * @param string                            $subject
     * @param string                            $channel
     * @param array                             $info
     * @param int|null                          $expirationTime
     * @param string|null                       $base64info
     * @param int|null                          $subscriptionExpirationTime
     * @param array<string>                     $audiences
     * @param string|null                       $issuer
     * @param int|null                          $issuedAt
     * @param string|null                       $jwtId
     * @param JwtPayloadForChannelOverride|null $override
     */
    public function __construct(private readonly string $subject, private readonly string $channel, array $info = [], ?int $expirationTime = null, ?string $base64info = null, private readonly ?int $subscriptionExpirationTime = null, private readonly array $audiences = [], private readonly ?string $issuer = null, private readonly ?int $issuedAt = null, private readonly ?string $jwtId = null, private readonly ?JwtPayloadForChannelOverride $override = null)
    {
        parent::__construct($info, $expirationTime, $base64info);
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getChannel(): string
    {
        return $this->channel;
    }

    /**
     * @return int|null
     */
    public function getSubscriptionExpirationTime(): ?int
    {
        return $this->subscriptionExpirationTime;
    }

    /**
     * @return array<string>
     */
    public function getAudiences(): array
    {
        return $this->audiences;
    }

    /**
     * @return string|null
     */
    public function getIssuer(): ?string
    {
        return $this->issuer;
    }

    /**
     * @return int|null
     */
    public function getIssuedAt(): ?int
    {
        return $this->issuedAt;
    }

    /**
     * @return string|null
     */
    public function getJwtId(): ?string
    {
        return $this->jwtId;
    }

    /**
     * @return JwtPayloadForChannelOverride|null
     */
    public function getOverride(): ?JwtPayloadForChannelOverride
    {
        return $this->override;
    }

    /**
     * {@inheritdoc}
     */
    #[ArrayShape([
        'sub' => 'string',
        'channel' => 'string',
        'info' => 'mixed',
        'b64info' => 'string|null',
        'exp' => 'int|null',
        'expire_at' => 'int|null',
        'aud' => 'array',
        'iss' => 'string|null',
        'iat' => 'int|null',
        'jti' => 'string|null',
        'override' => 'array|null',
    ])]
    public function getPayloadData(): array
    {
        $data = [
            'sub' => $this->getSubject(),
            'channel' => $this->getChannel(),
        ];

        if ([] !== $this->getInfo()) {
            $data['info'] = $this->getInfo();
        }

        if (null !== $this->getBase64Info()) {
            $data['b64info'] = $this->getBase64Info();
        }

        if (null !== $this->getExpirationTime()) {
            $data['exp'] = $this->getExpirationTime();
        }

        if (null !== $this->getSubscriptionExpirationTime()) {
            $data['expire_at'] = $this->getSubscriptionExpirationTime();
        }

        if ([] !== $this->getAudiences()) {
            $data['aud'] = $this->getAudiences();
        }

        if (null !== $this->getIssuer()) {
            $data['iss'] = $this->getIssuer();
        }

        if (null !== $this->getIssuedAt()) {
            $data['iat'] = $this->getIssuedAt();
        }

        if (null !== $this->getJwtId()) {
            $data['jti'] = $this->getJwtId();
        }

        if (null !== $this->getOverride()) {
            $data['override'] = $this->getOverride()->getPayloadData();
        }

        return $data;
    }
}
