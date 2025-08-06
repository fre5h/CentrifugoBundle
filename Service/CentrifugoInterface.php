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
use Fresh\CentrifugoBundle\Model\Disconnect;
use Fresh\CentrifugoBundle\Model\Override;
use Fresh\CentrifugoBundle\Model\StreamPosition;

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
     * @param string               $base64data
     */
    public function publish(array $data, string $channel, bool $skipHistory = false, array $tags = [], string $base64data = ''): void;

    /**
     * @param array<string, mixed> $data
     * @param string[]             $channels
     * @param bool                 $skipHistory
     * @param array<string, mixed> $tags
     * @param string               $base64data
     */
    public function broadcast(array $data, array $channels, bool $skipHistory = false, array $tags = [], string $base64data = ''): void;

    /**
     * @param string              $user
     * @param string              $channel
     * @param array               $info
     * @param string|null         $base64Info
     * @param string|null         $client
     * @param string|null         $session
     * @param array               $data
     * @param string|null         $base64Data
     * @param StreamPosition|null $recoverSince
     * @param Override|null       $override
     */
    public function subscribe(string $user, string $channel, array $info = [], ?string $base64Info = null, ?string $client = null, ?string $session = null, array $data = [], ?string $base64Data = null, ?StreamPosition $recoverSince = null, ?Override $override = null): void;

    /**
     * @param string $user
     * @param string $channel
     * @param string $client
     * @param string $session
     */
    public function unsubscribe(string $user, string $channel, string $client = '', string $session = ''): void;

    /**
     * @param string          $user
     * @param string[]        $whitelist
     * @param string|null     $client
     * @param string|null     $session
     * @param Disconnect|null $disconnectObject
     */
    public function disconnect(string $user, array $whitelist = [], ?string $client = null, ?string $session = null, ?Disconnect $disconnectObject = null): void;

    /**
     * @param string      $user
     * @param string|null $client
     * @param string|null $session
     * @param bool|null   $expired
     * @param int|null    $expireAt
     */
    public function refresh(string $user, ?string $client = null, ?string $session = null, ?bool $expired = null, ?int $expireAt = null): void;

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
     * @param string              $channel
     * @param bool                $reverse
     * @param int|null            $limit
     * @param StreamPosition|null $streamPosition
     *
     * @return array
     */
    public function history(string $channel, bool $reverse = false, ?int $limit = null, ?StreamPosition $streamPosition = null): array;

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
