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
 * HistoryRemoveCommand.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class HistoryRemoveCommand extends AbstractCommand
{
    use ChannelCommandTrait;

    /**
     * @param string $channel
     */
    public function __construct(protected readonly string $channel)
    {
        parent::__construct(
            Method::HISTORY_REMOVE,
            [
                'channel' => $channel,
            ]
        );
    }
}
