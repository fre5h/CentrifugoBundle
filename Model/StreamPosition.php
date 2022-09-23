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
 * StreamPosition.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class StreamPosition
{
    /**
     * @param int|null    $offset
     * @param string|null $epoch
     */
    public function __construct(private readonly ?int $offset = null, private readonly ?string $epoch = null)
    {
    }

    /**
     * @return int|null
     */
    public function getOffset(): ?int
    {
        return $this->offset;
    }

    /**
     * @return string|null
     */
    public function getEpoch(): ?string
    {
        return $this->epoch;
    }
}
