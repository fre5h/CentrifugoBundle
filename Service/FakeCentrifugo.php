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

namespace Fresh\CentrifugoBundle\Service;

use Fresh\CentrifugoBundle\Model\Disconnect;
use Fresh\CentrifugoBundle\Model\Override;
use Fresh\CentrifugoBundle\Model\StreamPosition;

/**
 * FakeCentrifugo.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
class FakeCentrifugo implements CentrifugoInterface
{
    /**
     * {@inheritdoc}
     */
    public function publish(array $data, string $channel, bool $skipHistory = false, array $tags = [], string $base64data = ''): void
    {
        // noop
    }

    /**
     * {@inheritdoc}
     */
    public function broadcast(array $data, array $channels, bool $skipHistory = false, array $tags = [], string $base64data = ''): void
    {
        // noop
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe(string $user, string $channel, array $info = [], ?string $base64Info = null, ?string $client = null, ?string $session = null, array $data = [], ?string $base64Data = null, ?StreamPosition $recoverSince = null, ?Override $override = null): void
    {
        // noop
    }

    /**
     * {@inheritdoc}
     */
    public function unsubscribe(string $user, string $channel, string $client = '', string $session = ''): void
    {
        // noop
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect(string $user, array $whitelist = [], ?string $client = null, ?string $session = null, ?Disconnect $disconnectObject = null): void
    {
        // noop
    }

    /**
     * {@inheritdoc}
     */
    public function refresh(string $user, ?string $client = null, ?string $session = null, ?bool $expired = null, ?int $expireAt = null): void
    {
        // noop
    }

    /**
     * {@inheritdoc}
     */
    public function presence(string $channel): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function presenceStats(string $channel): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function history(string $channel, bool $reverse = false, ?int $limit = null, ?StreamPosition $streamPosition = null): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function historyRemove(string $channel): void
    {
        // noop
    }

    /**
     * {@inheritdoc}
     */
    public function channels(?string $pattern = null): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function info(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function batchRequest(array $commands): array
    {
        return [];
    }
}
