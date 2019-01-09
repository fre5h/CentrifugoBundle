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
 * JsonWebTokenPayloadInterface.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
interface JsonWebTokenPayloadInterface
{
    /**
     * @return string
     */
    public function getSubject(): string;

    /**
     * @return int|null
     */
    public function getExpirationTime(): ?int;

    /**
     * @return array
     */
    public function getInfo(): array;

    /**
     * @return string|null
     */
    public function getBase64Info(): ?string;
}
