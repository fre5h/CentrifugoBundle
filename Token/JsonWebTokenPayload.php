<?php
/*
 * This file is part of the FreshCentrifugoBundle
 *
 * (c) Artem Henvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Fresh\CentrifugoBundle\Token;

/**
 * JsonWebTokenPayload.
 *
 * You can set only claims, that are supported by Centrifugo.
 * @see https://centrifugal.github.io/centrifugo/server/authentication/#claims
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class JsonWebTokenPayload implements JsonWebTokenPayloadInterface
{
    private $subject;
    private $expirationTime;
    private $info;
    private $base64info;

    /**
     * @param string      $subject
     * @param array       $info
     * @param int|null    $expirationTime
     * @param string|null $base64info
     */
    public function __construct(string $subject, array $info = [], ?int $expirationTime = null, ?string $base64info = null)
    {
        $this->subject = $subject;
        $this->info = $info;
        $this->expirationTime = $expirationTime;
        $this->base64info = $base64info;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpirationTime(): ?int
    {
        return $this->expirationTime;
    }

    /**
     * {@inheritdoc}
     */
    public function getInfo(): array
    {
        return $this->info;
    }

    /**
     * {@inheritdoc}
     */
    public function getBase64Info(): ?string
    {
        return $this->base64info;
    }
}
