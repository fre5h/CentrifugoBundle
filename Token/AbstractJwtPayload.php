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
 * AbstractJwtPayload.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
abstract class AbstractJwtPayload implements JwtPayloadInterface
{
    /**
     * @param array       $info
     * @param int|null    $expirationTime
     * @param string|null $base64info
     */
    public function __construct(private readonly array $info = [], private readonly ?int $expirationTime = null, private readonly ?string $base64info = null)
    {
    }

    /**
     * @return int|null
     */
    public function getExpirationTime(): ?int
    {
        return $this->expirationTime;
    }

    /**
     * @return array<string, mixed>
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
}
