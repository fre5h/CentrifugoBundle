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
 * DisconnectCommand.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class DisconnectCommand extends AbstractCommand
{
    /**
     * @param string $user
     */
    public function __construct(string $user)
    {
        parent::__construct(
            Method::DISCONNECT,
            [
                'user' => $user,
            ]
        );
    }
}
