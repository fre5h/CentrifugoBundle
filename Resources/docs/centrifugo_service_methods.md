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
    /**
     * @param CentrifugoInterface $centrifugo
     */
    public function __construct(private readonly CentrifugoInterface $centrifugo)
    {
    }
}
```

### Publish data into channel

```php
// With only required parameters
$this->centrifugo->publish(data: ['foo' => 'bar'], channel: 'channelA');
// With all optional parameters
$this->centrifugo->publish(data: ['foo' => 'bar'], channel: 'channelA', skipHistory: true, tags: ['tag' => 'value'], b64data: 'SGVsbG8gd29ybGQ=');
```

### Publish same data into many channels

```php
$this->centrifugo->broadcast(['foo' => 'bar'], ['channelA', 'channelB']);
```

### Unsubscribe user from channel

```php
$this->centrifugo->unsubscribe('user1', 'channelA');
```

### Disconnect user by ID

```php
$this->centrifugo->disconnect('user1');
```

### Get channel presence information (all clients currently subscribed on this channel)

```php
$data = $this->centrifugo->presence('channelA');

/*
 * print_r($data);
 *
 * Array
 * (
 *     [presence] => Array
 *         (
 *             [c54313b2-0442-499a-a70c-051f8588020f] => Array
 *                 (
 *                     [client] => c54313b2-0442-499a-a70c-051f8588020f
 *                     [user] => 42
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
$data = $this->centrifugo->history('channelA');

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
 * )
 */
```

### Remove history for channel

```php
$this->centrifugo->historyRemove('channelA');
```

### Get list of active (with one or more subscribers) channels

```php
$data = $this->centrifugo->channels();

/*
 * print_r($data);
 *
 * Array
 * (
 *     [channels] => Array
 *         (
 *             [0] => channelA
 *             [1] => channelB
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
 *             [name] => Alexanders-MacBook-Pro.local_8000
 *             [num_channels] => 0
 *             [num_clients] => 0
 *             [num_users] => 0
 *             [uid] => f844a2ed-5edf-4815-b83c-271974003db9
 *             [uptime] => 0
 *             [version] => 
 *             [metrics] => Array
 *                 (
 *                     ...
 *                 )
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
