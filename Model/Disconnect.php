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

namespace Fresh\CentrifugoBundle\Model;

/**
 * Disconnect.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class Disconnect
{
    /**
     * @param int    $code
     * @param string $reason
     */
    public function __construct(private readonly int $code, private readonly string $reason)
    {
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getReason(): string
    {
        return $this->reason;
    }
}
