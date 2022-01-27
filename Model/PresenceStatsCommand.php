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
 * PresenceStatsCommand.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class PresenceStatsCommand extends AbstractCommand implements ResultableCommandInterface
{
    use ChannelCommandTrait;

    /**
     * @param string $channel
     */
    #[Pure]
    public function __construct(string $channel)
    {
        $this->channel = $channel;

        parent::__construct(
            Method::PRESENCE_STATS,
            [
                'channel' => $channel,
            ]
        );
    }
}
