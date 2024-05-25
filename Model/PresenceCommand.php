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
 * PresenceCommand.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class PresenceCommand extends AbstractCommand implements ResultableCommandInterface
{
    use ChannelCommandTrait;

    /**
     * @param string $channel
     */
    public function __construct(protected readonly string $channel)
    {
        parent::__construct(
            Method::PRESENCE,
            [
                'channel' => $channel,
            ]
        );
    }

    public function processResponse(array $data): array
    {
        return $data['result']['presence'];
    }
}
