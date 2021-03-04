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
     * @param array  $data
     * @param string $channel
     */
    public function __construct(array $data, string $channel)
    {
        $this->channel = $channel;

        parent::__construct(
            Method::PUBLISH,
            [
                'channel' => $channel,
                'data' => $data,
            ]
        );
    }
}
