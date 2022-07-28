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
     * @param array    $data
     * @param string[] $channels
     */
    public function __construct(array $data, private readonly array $channels)
    {
        parent::__construct(
            Method::BROADCAST,
            [
                'channels' => $channels,
                'data' => $data,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getChannels(): iterable
    {
        return $this->channels;
    }
}
