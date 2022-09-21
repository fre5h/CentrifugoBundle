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
 * UnsubscribeCommand.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class UnsubscribeCommand extends AbstractCommand
{
    use ChannelCommandTrait;

    /**
     * @param string $user
     * @param string $channel
     * @param string $client
     * @param string $session
     */
    public function __construct(string $user, protected readonly string $channel, string $client = '', string $session = '')
    {
        $params = [
            'channel' => $channel,
            'user' => $user,
        ];

        if (!empty($client)) {
            $params['client'] = $client;
        }

        if (!empty($session)) {
            $params['session'] = $session;
        }

        parent::__construct(Method::UNSUBSCRIBE, $params);
    }
}
