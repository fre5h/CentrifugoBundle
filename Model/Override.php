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
 * Override.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class Override
{
    /**
     * @param bool $presence
     * @param bool $joinLeave
     * @param bool $forcePushJoinLeave
     * @param bool $forcePositioning
     * @param bool $forceRecovery
     */
    public function __construct(private readonly bool $presence, private readonly bool $joinLeave, private readonly bool $forcePushJoinLeave, private readonly bool $forcePositioning, private readonly bool $forceRecovery)
    {
    }

    public function isPresence(): bool
    {
        return $this->presence;
    }

    public function isJoinLeave(): bool
    {
        return $this->joinLeave;
    }

    public function isForcePushJoinLeave(): bool
    {
        return $this->forcePushJoinLeave;
    }

    public function isForcePositioning(): bool
    {
        return $this->forcePositioning;
    }

    public function isForceRecovery(): bool
    {
        return $this->forceRecovery;
    }
}
