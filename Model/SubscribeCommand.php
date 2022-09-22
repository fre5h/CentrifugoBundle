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
     * @param string|null $base64Info
     * @param string|null $client
     * @param string|null $session
     * @param array       $data
     */
    public function __construct(string $user, string $channel, array $info = [], ?string $base64Info = null, ?string $client = null, ?string $session = null, array $data = [], ?string $base64Data = null)
    {
        $params = [
            'user' => $user,
            'channel' => $channel,
        ];

        if (!empty($info)) {
            $params['info'] = $info;
        }

        if (\is_string($base64Info) && !empty($base64Info)) {
            $params['b64info'] = $base64Info;
        }

        if (\is_string($client) && !empty($client)) {
            $params['client'] = $client;
        }

        if (\is_string($client) && !empty($session)) {
            $params['session'] = $session;
        }

        if (!empty($data)) {
            $params['data'] = $data;
        }

        if (\is_string($base64Data) && !empty($base64Data)) {
            $params['b64data'] = $base64Data;
        }

        parent::__construct(Method::REFRESH, $params);
    }
}
