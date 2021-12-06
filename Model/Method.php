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
 * Method.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class Method
{
    public const PUBLISH = 'publish';

    public const BROADCAST = 'broadcast';

    public const SUBSCRIBE = 'subscribe';

    public const UNSUBSCRIBE = 'unsubscribe';

    public const DISCONNECT = 'disconnect';

    public const REFRESH = 'refresh';

    public const PRESENCE = 'presence';

    public const PRESENCE_STATS = 'presence_stats';

    public const HISTORY = 'history';

    public const HISTORY_REMOVE = 'history_remove';

    public const CHANNELS = 'channels';

    public const INFO = 'info';
}
