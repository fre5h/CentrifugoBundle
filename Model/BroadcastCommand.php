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
 * BroadcastCommand.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class BroadcastCommand extends AbstractCommand
{
    /**
     * @param array<string, mixed> $data
     * @param string[]             $channels
     * @param bool                 $skipHistory
     * @param array<string, mixed> $tags
     * @param string               $base64data
     */
    public function __construct(array $data, private array $channels, bool $skipHistory = false, array $tags = [], string $base64data = '')
    {
        $params = [
            'channels' => $channels,
            'data' => $data,
        ];

        if ($skipHistory) {
            $params['skip_history'] = $skipHistory;
        }

        if (!empty($tags)) {
            $params['tags'] = $tags;
        }

        if (!empty($base64data)) {
            $params['base64data'] = $base64data;
        }

        parent::__construct(Method::BROADCAST, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function getChannels(): iterable
    {
        return $this->channels;
    }
}
