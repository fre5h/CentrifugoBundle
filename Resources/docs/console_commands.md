🔝 [Back to index](./../../README.md "Back to index")

# Console commands ⚒️

### `centrifugo:publish`

```bash
$ bin/console centrifugo:publish '{"foo":"bar"}' channelAbc

[OK] DONE
```

### `centrifugo:broadcast`

```bash
$ bin/console centrifugo:broadcast '{"foo":"bar"}' channelAbc channelDef

[OK] DONE
```

### `centrifugo:unsubscribe`

```bash
$ bin/console centrifugo:unsubscribe user123 channelAbc

[OK] DONE
```

### `centrifugo:disconnect`

```bash
$ bin/console centrifugo:disconnect user123

[OK] DONE
```

### `centrifugo:presence`

```bash
$ bin/console centrifugo:presence channelAbc

Presence
========

 c54313b2-0442-499a-a70c-051f8588020f
   ├ client: c54313b2-0442-499a-a70c-051f8588020f
   └ user: 42
 adad13b1-0442-499a-a70c-051f858802da
   ├ client: adad13b1-0442-499a-a70c-051f858802da
   └ user: 42
```

### `centrifugo:presence-stats`

```bash
$ bin/console centrifugo:presence-stats channelAbc

Presence Stats
==============

 num_clients: 4
 num_users: 3
```

### `centrifugo:history`

```bash
$ bin/console centrifugo:history channelAbc

Publications
============

{
    "key": "value"
}

------------

{
    "foo": "bar",
    "baz": "qux"
}

------------
```

### `centrifugo:history-remove`

```bash
$ bin/console centrifugo:history-remove channelAbc

[OK] DONE
```

### `centrifugo:channels`

```bash
$ bin/console centrifugo:channels

Channels
========

 * chat
 * notification

 TOTAL: 2
```

### `centrifugo:info`

```bash
$ bin/console centrifugo:info

Info
====

Node c980f44237d6_8000
----------------------

 uid: 9d1f429c-63b3-4a39-969a-4df9cd46030f
 name: c980f44237d6_8000
 version: 2.4.0
 num_clients: 0
 num_users: 0
 num_channels: 0
 uptime: 53183
 metrics
   ├ interval: 60
   └ items
     ├ centrifuge.node.build.version.2.4.0: 1
     ...
     └ process.virtual.memory_max_bytes: -1
```

## More features

* [Back to index](./../../README.md "Back to index")
* [Examples of using Centrifugo service](./centrifugo_service_methods.md "Examples of using Centrifugo service")
* [Authentication with JWT tokens](./authentication.md "Authentication with JWT tokens")
