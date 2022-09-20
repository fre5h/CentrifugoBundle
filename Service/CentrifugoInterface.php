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

use Fresh\CentrifugoBundle\Model\CommandInterface;

/**
 * CentrifugoInterface.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
interface CentrifugoInterface
{
    /**
     * @param array<string, mixed> $data
     * @param string               $channel
     * @param bool                 $skipHistory
     * @param array<string, mixed> $tags
     * @param string               $b64data
     */
    public function publish(array $data, string $channel, bool $skipHistory = false, array $tags = [], string $b64data = ''): void;

    /**
     * @param array<string, mixed> $data
     * @param string[]             $channels
     * @param bool                 $skipHistory
     * @param array<string, mixed> $tags
     * @param string               $b64data
     */
    public function broadcast(array $data, array $channels, bool $skipHistory = false, array $tags = [], string $b64data = ''): void;

    /**
     * @param string $user
     * @param string $channel
     */
    public function unsubscribe(string $user, string $channel): void;

    /**
     * @param string $user
     */
    public function disconnect(string $user): void;

    /**
     * @param string $channel
     *
     * @return array
     */
    public function presence(string $channel): array;

    /**
     * @param string $channel
     *
     * @return array
     */
    public function presenceStats(string $channel): array;

    /**
     * @param string $channel
     *
     * @return array
     */
    public function history(string $channel): array;

    /**
     * @param string $channel
     */
    public function historyRemove(string $channel): void;

    /**
     * @param string|null $pattern
     *
     * @return array
     */
    public function channels(?string $pattern = null): array;

    /**
     * @return array
     */
    public function info(): array;

    /**
     * @param CommandInterface[] $commands
     *
     * @return array
     */
    public function batchRequest(array $commands): array;
}
