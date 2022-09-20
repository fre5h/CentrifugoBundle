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
 * PublishCommand.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class PublishCommand extends AbstractCommand
{
    use ChannelCommandTrait;

    /**
     * @param array<string, mixed> $data
     * @param string               $channel
     * @param bool                 $skipHistory
     * @param array<string, mixed> $tags
     * @param string               $base64data
     */
    public function __construct(readonly array $data, protected readonly string $channel, readonly bool $skipHistory = false, readonly array $tags = [], readonly string $base64data = '')
    {
        $params = [
            'channel' => $channel,
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

        parent::__construct(Method::PUBLISH, $params);
    }
}
