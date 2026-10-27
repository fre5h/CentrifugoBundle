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
 * PrivateChannelAuthenticatorResolver.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
class PrivateChannelAuthenticatorResolver
{
    /**
     * @param ChannelAuthenticatorInterface[]|iterable $channelAuthenticators
     */
    public function __construct(private readonly iterable $channelAuthenticators)
    {
    }

    /**
     * @param string  $channel
     * @param Request $request
     *
     * @return ChannelAuthenticatorInterface|null
     */
    public function findAppropriateChannelAuthenticator(string $channel, Request $request): ?ChannelAuthenticatorInterface
    {
        foreach ($this->channelAuthenticators as $channelAuthenticator) {
            if ($channelAuthenticator->supports($channel, $request)) {
                return $channelAuthenticator;
            }
        }

        return null;
    }
}
