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
    use ChannelCommandTrait;

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
    public function __construct(string $user, protected readonly string $channel, array $info = [], ?string $base64Info = null, ?string $client = null, ?string $session = null, array $data = [], ?string $base64Data = null, ?StreamPosition $recoverSince = null, ?Override $override = null)
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

        if ($recoverSince instanceof StreamPosition) {
            $recoverSinceParams = [];

            if (\is_int($recoverSince->getOffset()) && $recoverSince->getOffset() > 0) {
                $recoverSinceParams['offset'] = $recoverSince->getOffset();
            }

            if (\is_string($recoverSince->getEpoch()) && !empty($recoverSince->getEpoch())) {
                $recoverSinceParams['epoch'] = $recoverSince->getEpoch();
            }

            if ([] !== $recoverSinceParams) {
                $params['recover_since'] = $recoverSinceParams;
            }
        }

        if ($override instanceof Override) {
            $params['override'] = [
                'presence' => $override->isPresence(),
                'join_leave' => $override->isJoinLeave(),
                'force_push_join_leave' => $override->isForcePushJoinLeave(),
                'force_positioning' => $override->isForcePositioning(),
                'force_recovery' => $override->isForceRecovery(),
            ];
        }

        parent::__construct(Method::SUBSCRIBE, $params);
    }
}
