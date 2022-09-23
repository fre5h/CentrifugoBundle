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
 * HistoryCommand.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class HistoryCommand extends AbstractCommand implements ResultableCommandInterface
{
    use ChannelCommandTrait;

    /**
     * @param string              $channel
     * @param bool                $reverse
     * @param int|null            $limit
     * @param StreamPosition|null $streamPosition
     */
    public function __construct(protected readonly string $channel, bool $reverse = false, ?int $limit = null, ?StreamPosition $streamPosition = null)
    {
        $params = [
            'channel' => $channel,
        ];

        if ($reverse) {
            $params['reverse'] = $reverse;
        }

        if (\is_int($limit) && $limit > 0) {
            $params['limit'] = $limit;
        }

        if ($streamPosition instanceof StreamPosition) {
            if (\is_int($streamPosition->getOffset()) && $streamPosition->getOffset() > 0) {
                $params['since']['offset'] = $streamPosition->getOffset();
            }

            if (\is_string($streamPosition->getEpoch()) && !empty($streamPosition->getEpoch())) {
                $params['since']['epoch'] = $streamPosition->getEpoch();
            }
        }

        parent::__construct(Method::HISTORY, $params);
    }
}
