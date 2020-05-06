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

/**
 * JwtPayload.
 *
 * @see https://centrifugal.github.io/centrifugo/server/authentication/#claims
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class JwtPayload
{
    /** @var string */
    private $subject;

    /** @var int|null */
    private $expirationTime;

    /** @var array */
    private $info;

    /** @var string|null */
    private $base64info;

    /** @var string[] */
    private $channels;

    /**
     * @param string      $subject
     * @param array       $info
     * @param int|null    $expirationTime
     * @param string|null $base64info
     * @param string[]    $channels
     */
    public function __construct(string $subject, array $info = [], ?int $expirationTime = null, ?string $base64info = null, array $channels = [])
    {
        $this->subject = $subject;
        $this->info = $info;
        $this->expirationTime = $expirationTime;
        $this->base64info = $base64info;
        $this->channels = $channels;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return int|null
     */
    public function getExpirationTime(): ?int
    {
        return $this->expirationTime;
    }

    /**
     * @return array
     */
    public function getInfo(): array
    {
        return $this->info;
    }

    /**
     * @return string|null
     */
    public function getBase64Info(): ?string
    {
        return $this->base64info;
    }

    /**
     * @return array
     */
    public function getChannels(): array
    {
        return $this->channels;
    }
}
