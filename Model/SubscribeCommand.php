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
 * SubscribeCommand.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class SubscribeCommand extends AbstractCommand
{
    /**
     * @param string      $user
     * @param string      $channel
     * @param array       $info
     * @param string|null $base64data
     * @param string|null $client
     * @param string|null $session
     */
    public function __construct(string $user, string $channel, array $info = [], ?string $base64data = null, ?string $client = null, ?string $session = null)
    {
        $params = [
            'user' => $user,
            'channel' => $channel,
        ];

        if (!empty($info)) {
            $params['info'] = $info;
        }

        if (\is_string($base64data) && !empty($base64data)) {
            $params['b64data'] = $base64data;
        }

        if (\is_string($client) && !empty($client)) {
            $params['client'] = $client;
        }

        if (\is_string($client) && !empty($session)) {
            $params['session'] = $session;
        }

        parent::__construct(Method::REFRESH, $params);
    }
}
