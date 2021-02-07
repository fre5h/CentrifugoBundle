🔝 [Back to index](./../../README.md "Back to index")

# Configuration ⚙

By default, CentrifugoBundle doesn't have required configuration parameters. It works with default values.
But you can customize bundle configuration for your own needs.

```yaml
# config/packages/fresh_centrifugo.yaml

fresh_centrifugo:
    # Default value is 255, if you changed it in Centrifugo configuration,
    # then you can change it in bundle configuration, it effects only on validation
    channel_max_length: 255
    jwt:
        # You can set default TTL for all JWT tokens, if it not set, then default value is NULL
        # Default NULL value means that tokens will not be expired
        ttl: 86400 
    fake_mode: true # Enables fake mode for Centrifugo client, no real request will be sent.
    api_key: '%env(CENTRIFUGO_API_KEY)%' # You can change API key here
    api_endpoint: '%env(CENTRIFUGO_API_ENDPOINT)%' # You can change API endpoint here
    secret: '%env(CENTRIFUGO_SECRET)%' # You can change secret here
```

## More features

* [Back to index](./../../README.md "Back to index")
* [Examples of using Centrifugo service](./centrifugo_service_methods.md "Examples of using Centrifugo service")
* [Examples of using console commands](./console_commands.md "Examples of using console commands")
* [Authentication with JWT tokens](./authentication.md "Authentication with JWT tokens")
