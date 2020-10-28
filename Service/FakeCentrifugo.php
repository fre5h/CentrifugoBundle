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
    public function publish(array $data, string $channel): void
    {
        // noop
    }

    /**
     * {@inheritdoc}
     */
    public function broadcast(array $data, array $channels): void
    {
        // noop
    }

    /**
     * {@inheritdoc}
     */
    public function unsubscribe(string $user, string $channel): void
    {
        // noop
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect(string $user): void
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
    public function history(string $channel): array
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
    public function channels(): array
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
