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
    private string $user;

    /**
     * @param string      $user
     * @param string|null $client
     * @param bool|null   $expired
     * @param int|null    $expireAt
     */
    public function __construct(string $user, ?string $client = null, ?bool $expired = null, ?int $expireAt = null)
    {
        $this->user = $user;

        parent::__construct(
            Method::REFRESH,
            [
                'user' => $this->user,
                'client' => $client,
                'expired' => $expired,
                'expire_at' => $expireAt,
            ]
        );
    }
}
