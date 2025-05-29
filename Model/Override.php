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

    /**
     * @return bool
     */
    public function isPresence(): bool
    {
        return $this->presence;
    }

    /**
     * @return bool
     */
    public function isJoinLeave(): bool
    {
        return $this->joinLeave;
    }

    /**
     * @return bool
     */
    public function isForcePushJoinLeave(): bool
    {
        return $this->forcePushJoinLeave;
    }

    /**
     * @return bool
     */
    public function isForcePositioning(): bool
    {
        return $this->forcePositioning;
    }

    /**
     * @return bool
     */
    public function isForceRecovery(): bool
    {
        return $this->forceRecovery;
    }
}
