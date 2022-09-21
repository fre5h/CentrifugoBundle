🔝 [Back to index](./../../README.md "Back to index")

# Console commands ⚒️

### `centrifugo:publish`

```bash
$ bin/console centrifugo:publish '{"foo":"bar"}' channelName

[OK] DONE
```

```bash
$ bin/console centrifugo:publish '{"foo":"bar"}' channelName --tags='{"tag":"value"}' --base64data=SGVsbG8gd29ybGQ= --skipHistory

[OK] DONE
```

### `centrifugo:broadcast`

```bash
$ bin/console centrifugo:broadcast '{"foo":"bar"}' channelName1 channelName2

[OK] DONE
```

```bash
$ bin/console centrifugo:broadcast '{"foo":"bar"}' channelName1 channelName2 --tags='{"tag":"value"}' --base64data=SGVsbG8gd29ybGQ= --skipHistory

[OK] DONE
```

### `centrifugo:subscribe`

```bash
$ bin/console centrifugo:subscribe @todo Finish

[OK] DONE
```

### `centrifugo:unsubscribe`

```bash
$ bin/console centrifugo:unsubscribe user123 channelName

[OK] DONE
```

```bash
$ bin/console centrifugo:unsubscribe user123 channelName --client=clientID --session=sessionID

[OK] DONE
```

### `centrifugo:disconnect`

```bash
$ bin/console centrifugo:disconnect user123

[OK] DONE
```

```bash
$ bin/console centrifugo:disconnect user123 --whitelist=clientID1 --client=clientID2 --session=sessionID --disconnectCode=999 --disconnectReason="some reason"

[OK] DONE
```

### `centrifugo:refresh`

```bash
$ bin/console centrifugo:refresh user123

[OK] DONE
```

```bash
$ bin/console centrifugo:refresh user123 --client=clientID2 --session=sessionID --expired

[OK] DONE
```

```bash
$ bin/console centrifugo:refresh user123 --expireAt=1234567890

[OK] DONE
```

### `centrifugo:presence`

```bash
$ bin/console centrifugo:presence channelName

Presence
========

 f067a54a-6f68-47d2-aaf0-6e41dcb8f297
   ├ client: f067a54a-6f68-47d2-aaf0-6e41dcb8f297
   ├ conn_info:
   │ {
   │     "username": "user1@test.com"
   │ }
   ├ chan_info:
   │ {
   │     "foo": "bar"
   │ }
   └ user: bcb8c9dd-eba7-4ef6-8b86-0d6a2b1455a1
```

### `centrifugo:presence-stats`

```bash
$ bin/console centrifugo:presence-stats channelName

Presence Stats
==============

 Total number of clients in channel: 4
 Total number of unique users in channel: 3
```

### `centrifugo:history`

```bash
$ bin/console centrifugo:history channelName

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

 Limit: 10
 Offset: 10
 Epoch: WGHG
```

```bash
$ bin/console centrifugo:history channelName --limit=2 --offset=1 --epoch=ABCD --reverse

Publications
============

{
    "foo": "bar",
    "baz": "qux"
}

------------

{
    "key": "value"
}

------------

 Limit: 2
 Offset: 1
 Epoch: ABCD
```

### `centrifugo:history-remove`

```bash
$ bin/console centrifugo:history-remove channelName

[OK] DONE
```

### `centrifugo:channels`

```bash
$ bin/console centrifugo:channels

 -------------- ------------------------- 
  Channel Name   Number Of Subscriptions  
 -------------- ------------------------- 
  channelA       25
  channelB       33
 -------------- ------------------------- 

 Total Channels: 2
```

```bash
$ bin/console centrifugo:channels channelA

 -------------- ------------------------- 
  Channel Name   Number Of Subscriptions  
 -------------- ------------------------- 
  channelA       25
 -------------- ------------------------- 

 Total Channels: 1
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
 version: 4.0.1
 num_clients: 0
 num_users: 0
 num_channels: 0
 uptime: 31214
 metrics
   ├ interval: 60
   └ items
     ├ centrifugo.api.command_duration_seconds.count.method.broadcast.protocol.http: 0
     ...
     └ process.virtual.memory_max_bytes: 1.844674407371E+19
 num_subs: 0
```

## More features

* [Back to index](./../../README.md "Back to index")
* [Examples of using Centrifugo service](./centrifugo_service_methods.md "Examples of using Centrifugo service")
* [Authentication with JWT tokens](./authentication.md "Authentication with JWT tokens")
* [Customize bundle configuration](./configuration.md "Customize bundle configuration")
