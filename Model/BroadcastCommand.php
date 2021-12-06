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
    /** @var string[] */
    private array $channels;

    /**
     * @param array<string, mixed> $data
     * @param string[]             $channels
     * @param bool|null            $skipHistory
     */
    public function __construct(array $data, array $channels, ?bool $skipHistory = null)
    {
        $this->channels = $channels;

        $params = [
            'channels' => $channels,
            'data' => $data,
        ];

        if (null !== $skipHistory) {
            $params['skip_history'] = $skipHistory;
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
