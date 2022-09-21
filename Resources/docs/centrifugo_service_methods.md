🔝 [Back to index](./../../README.md "Back to index")

# Using Centrifugo service 🧑‍🎓

## Inject dependency into your service

```php
<?php
declare(strict_types=1);

namespace App\Service;

use Fresh\CentrifugoBundle\Service\CentrifugoInterface;

class YourService
{
    public function __construct(private readonly CentrifugoInterface $centrifugo)
    {
    }
}
```

### Publish data into channel

```php
// With only required parameters
$this->centrifugo->publish(
    data: ['foo' => 'bar'],
    channel: 'channelA',
);

// With all optional parameters
$this->centrifugo->publish(
    data: ['foo' => 'bar'],
    channel: 'channelA',
    skipHistory: true,
    tags: ['tag' => 'value'],
    base64data: 'SGVsbG8gd29ybGQ=',
);
```

### Publish same data into many channels

```php
// With only required parameters
$this->centrifugo->broadcast(
    data: ['foo' => 'bar'],
    channels: ['channelA', 'channelB']
);
```

```php
// With all optional parameters
$this->centrifugo->broadcast(
    data: ['foo' => 'bar'],
    channels: ['channelA', 'channelB'],
    skipHistory: true,
    tags: ['tag' => 'value'],
    base64data: 'SGVsbG8gd29ybGQ=',
);
```

### Unsubscribe user from channel

```php
// With only required parameters
$this->centrifugo->unsubscribe(
    user: 'user1',
    channel: 'channelA',
);
```

```php
// With all optional parameters
$this->centrifugo->unsubscribe(
    user: 'user1',
    channel: 'channelA',
    client: 'client',
    session: 'session',
);
```

### Disconnect user by ID

```php
// With only required parameters
$this->centrifugo->disconnect(user: 'user1');
```

```php
// With all optional parameters
$this->centrifugo->disconnect(
    user: 'user1',
    whitelist: ['clientID2'],
    client: 'clientID2',
    session: 'sessionID',
    disconnectObject: new DisconnectObject(999, 'some reason'),
);
```

### Refresh user connection

```php
// With only required parameters
$this->centrifugo->refresh(user: 'user1');
```

```php
// With all optional parameters
$this->centrifugo->refresh(
    user: 'user1',
    client: 'test',
    session: 'test',
    expired: true,
    expireAt: 1234567890,
);
```

### Get channel presence information (all clients currently subscribed on this channel)

```php
$data = $this->centrifugo->presence(channel: 'channelA');

/*
 * print_r($data);
 *
 * Array
 * (
 *     [presence] => Array
 *         (
 *             [c54313b2-0442-499a-a70c-051f8588020f] => Array
 *                 (
 *                     [user] => 8ea9a26f-e5b3-4d61-b652-7e51e8cf78e3
 *                     [client] => 5533ab25-6899-4084-809f-090d87eb6e6f
 *                     [conn_info] => Array
 *                         (
 *                             [username] => user1@test.com
 *                         )
 *                     [chan_info] => Array
 *                         (
 *                             [foo] => bar
 *                         )
 *                 )
 *             [adad13b1-0442-499a-a70c-051f858802da] => Array
 *                 (
 *                     [client] => adad13b1-0442-499a-a70c-051f858802da
 *                     [user] => 42
 *                 )
 *         )
 * )
 */
```

### Get short channel presence information

```php
$data = $this->centrifugo->presenceStats('channelA');

/*
 * print_r($data);
 *
 * Array
 * (
 *     [num_clients] => 0
 *     [num_users] => 0
 * )
 */
```

### Get channel history information (list of last messages published into channel)

```php
// With only required parameters
$data = $this->centrifugo->history(channel: 'channelA');

/*
 * print_r($data);
 *
 * Array
 * (
 *     [publications] => Array
 *         (
 *             [0] => Array
 *                 (
 *                     [data] => Array
 *                         (
 *                             [text] => hello
 *                         )
 *                 )
 *             [1] => Array
 *                 (
 *                     [data] => Array
 *                         (
 *                             [text] => hi!
 *                         )
 *                 )
 *         )
 *     [offset] => 10
 *     [epoch] => ABCD
 * )
 */
```

```php
// With all optional parameters
$data = $this->centrifugo->history(
    channel: 'channelA',
    reverse: true,
    limit: 10,
    offset: 5,
    epoch: 'EFGH',
);

/*
 * print_r($data);
 *
 * Array
 * (
 *     [publications] => Array
 *         (
 *             [0] => Array
 *                 (
 *                     [data] => Array
 *                         (
 *                             [text] => hi!
 *                         )
 *                 )
 *             [1] => Array
 *                 (
 *                     [data] => Array
 *                         (
 *                             [text] => hello
 *                         )
 *                 )
 *         )
 *     [offset] => 5
 *     [epoch] => EFGH
 * )
 */
```

### Remove history for channel

```php
$this->centrifugo->historyRemove('channelA');
```

### Get list of active (with one or more subscribers) channels

```php
// Without pattern (all channels)
$data = $this->centrifugo->channels();

/*
 * print_r($data);
 *
 * Array
 * (
 *     [channels] => Array
 *         (
 *             [channelA] => Array
 *                 (
 *                     [num_clients] => 25
 *                 )
 *             [channelB] => Array
 *                 (
 *                     [num_clients] => 33
 *                 )
 *         )
 * )
 */
```

```php
// With pattern
$data = $this->centrifugo->channels(pattern: 'channelA');

/*
 * print_r($data);
 *
 * Array
 * (
 *     [channels] => Array
 *         (
 *             [channelA] => Array
 *                 (
 *                     [num_clients] => 25
 *                 )
 *         )
 * )
 */
```

### Get information about running Centrifugo nodes

```php
$data = $this->centrifugo->info();

/*
 * print_r($data);
 *
 * Array
 * (
 *     [nodes] => Array
 *         (
 *             [name] => 89673488918c_8000
 *             [num_channels] => 0
 *             [num_clients] => 0
 *             [num_users] => 0
 *             [uid] => 3cb0e756-5818-4cb1-9a4a-6fba4a369de6
 *             [uptime] => 31214
 *             [version] => 4.0.1
 *             [metrics] => Array
 *                 (
 *                     ...
 *                 )
 *             [num_subs:] => 0
 *         )
 * )
 */
```

### Batch request

```php
use Fresh\CentrifugoBundle\Model;

$publish = new Model\PublishCommand(['foo' => 'bar'], 'channelA');
$broadcast = new Model\BroadcastCommand(['baz' => 'qux'], ['channelB', 'channelC']);
$channels = new Model\PresenceStatsCommand();

$data = $this->centrifugo->batchRequest([$publish, $broadcast, $channels]);

/*
 * print_r($data);
 *
 * Array
 * (
 *     [0] => null
 *     [1] => null
 *     [2] => Array
 *         (
 *             [num_clients] => 0
 *             [num_users] => 0
 *         )
 * )
 */
```

## More features

* [Back to index](./../../README.md "Back to index")
* [Examples of using console commands](./console_commands.md "Examples of using console commands")
* [Authentication with JWT tokens](./authentication.md "Authentication with JWT tokens")
* [Customize bundle configuration](./configuration.md "Customize bundle configuration")
