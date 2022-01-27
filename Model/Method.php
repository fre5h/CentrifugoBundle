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
    public final const PUBLISH = 'publish';

    public final const BROADCAST = 'broadcast';

    public final const SUBSCRIBE = 'subscribe';

    public final const UNSUBSCRIBE = 'unsubscribe';

    public final const DISCONNECT = 'disconnect';

    public final const REFRESH = 'refresh';

    public final const PRESENCE = 'presence';

    public final const PRESENCE_STATS = 'presence_stats';

    public final const HISTORY = 'history';

    public final const HISTORY_REMOVE = 'history_remove';

    public final const CHANNELS = 'channels';

    public final const INFO = 'info';
}
