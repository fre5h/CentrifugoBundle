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
 * RefreshCommand.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class RefreshCommand extends AbstractCommand
{
    /**
     * @param string      $user
     * @param string|null $client
     * @param string|null $session
     * @param bool|null   $expired
     * @param int|null    $expireAt
     */
    public function __construct(string $user, ?string $client = null, ?string $session = null, ?bool $expired = null, ?int $expireAt = null)
    {
        $params = ['user' => $user];

        if (\is_string($client) && !empty($client)) {
            $params['client'] = $client;
        }

        if (\is_string($client) && !empty($session)) {
            $params['session'] = $session;
        }

        if (\is_bool($expired)) {
            $params['expired'] = $expired;
        }

        if (\is_int($expireAt) && $expireAt > 0) {
            $params['expire_at'] = $expireAt;
        }

        parent::__construct(Method::REFRESH, $params);
    }
}
