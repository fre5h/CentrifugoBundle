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

use JetBrains\PhpStorm\Pure;

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
     * @param string               $b64data
     */
    #[Pure]
    public function __construct(array $data, string $channel, bool $skipHistory = false, array $tags = [], string $b64data = '')
    {
        $this->channel = $channel;

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

        if (!empty($b64data)) {
            $params['b64data'] = $b64data;
        }

        parent::__construct(Method::PUBLISH, $params);
    }
}
