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
 * JwtPayloadForChannelOverride.
 *
 * @see https://centrifugal.dev/docs/server/channel_token_auth#override
 */
final class JwtPayloadForChannelOverride
{
    /**
     * @param bool|null $presence
     * @param bool|null $joinLeave
     * @param bool|null $forcePushJoinLeave
     * @param bool|null $forceRecovery
     * @param bool|null $forcePosting
     */
    public function __construct(private readonly ?bool $presence = null, private readonly ?bool $joinLeave = null, private readonly ?bool $forcePushJoinLeave = null, private readonly ?bool $forceRecovery = null, private readonly ?bool $forcePosting = null)
    {
    }

    /**
     * @return bool|null
     */
    public function getPresence(): ?bool
    {
        return $this->presence;
    }

    /**
     * @return bool|null
     */
    public function getJoinLeave(): ?bool
    {
        return $this->joinLeave;
    }

    /**
     * @return bool|null
     */
    public function getForcePushJoinLeave(): ?bool
    {
        return $this->forcePushJoinLeave;
    }

    /**
     * @return bool|null
     */
    public function getForceRecovery(): ?bool
    {
        return $this->forceRecovery;
    }

    /**
     * @return bool|null
     */
    public function getForcePosting(): ?bool
    {
        return $this->forcePosting;
    }

    /**
     * @return array|null
     */
    #[ArrayShape([
        'presence' => 'array|null',
        'join_leave' => 'array|null',
        'force_push_join_leave' => 'array|null',
        'force_recovery' => 'array|null',
        'force_posting' => 'array|null',
    ])]
    public function getPayloadData(): array
    {
        $data = [];

        if (null !== $this->getPresence()) {
            $data['presence']['value'] = $this->getPresence();
        }

        if (null !== $this->getJoinLeave()) {
            $data['join_leave']['value'] = $this->getJoinLeave();
        }

        if (null !== $this->getForcePushJoinLeave()) {
            $data['force_push_join_leave']['value'] = $this->getForcePushJoinLeave();
        }

        if (null !== $this->getForceRecovery()) {
            $data['force_recovery']['value'] = $this->getForceRecovery();
        }

        if (null !== $this->getForcePosting()) {
            $data['force_posting']['value'] = $this->getForcePosting();
        }

        return $data;
    }
}
