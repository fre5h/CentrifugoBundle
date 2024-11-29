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
     * @param string          $user
     * @param string[]        $clientIdWhitelist
     * @param string|null     $client
     * @param string|null     $session
     * @param Disconnect|null $disconnectObject
     */
    public function __construct(string $user, array $clientIdWhitelist = [], ?string $client = null, ?string $session = null, ?Disconnect $disconnectObject = null)
    {
        $params = [
            'user' => $user,
        ];

        if (!empty($clientIdWhitelist)) {
            $params['whitelist'] = $clientIdWhitelist;
        }

        if (\is_string($client) && !empty($client)) {
            $params['client'] = $client;
        }

        if (\is_string($session) && !empty($session)) {
            $params['session'] = $session;
        }

        if ($disconnectObject instanceof Disconnect) {
            $params['disconnect']['code'] = $disconnectObject->getCode();
            $params['disconnect']['reason'] = $disconnectObject->getReason();
        }

        parent::__construct(Method::DISCONNECT, $params);
    }
}
