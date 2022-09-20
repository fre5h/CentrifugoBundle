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
 * ChannelsCommand.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class ChannelsCommand extends AbstractCommand implements ResultableCommandInterface
{
    /**
     * @param string|null $pattern
     */
    #[Pure]
    public function __construct(?string $pattern = null)
    {
        $params = [];

        if (!empty($pattern)) {
            $params['pattern'] = $pattern;
        }

        parent::__construct(Method::CHANNELS, $params);
    }
}
