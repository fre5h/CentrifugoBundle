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

namespace Fresh\CentrifugoBundle\Service\ChannelAuthenticator;

use Symfony\Component\HttpFoundation\Request;

/**
 * ChannelAuthenticatorInterface.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
interface ChannelAuthenticatorInterface
{
    /**
     * @param string  $channel
     * @param Request $request
     *
     * @return bool
     */
    public function supports(string $channel, Request $request): bool;

    /**
     * @param string  $channel
     * @param Request $request
     *
     * @return bool
     */
    public function hasAccessToChannel(string $channel, Request $request): bool;
}
