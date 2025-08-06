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
enum Method: string
{
    case PUBLISH = 'publish';
    case BROADCAST = 'broadcast';
    case SUBSCRIBE = 'subscribe';
    case UNSUBSCRIBE = 'unsubscribe';
    case DISCONNECT = 'disconnect';
    case REFRESH = 'refresh';
    case PRESENCE = 'presence';
    case PRESENCE_STATS = 'presence_stats';
    case HISTORY = 'history';
    case HISTORY_REMOVE = 'history_remove';
    case CHANNELS = 'channels';
    case INFO = 'info';
    case BATCH = 'batch';
}
