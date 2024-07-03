🔝 [Back to index](./../../README.md "Back to index")

# Authentication with JWT token 🗝️️

## Anonymous

### Use `CredentialsGenerator` to generate an anonymous JWT token

```php
<?php
declare(strict_types=1);

namespace App\Controller;

use Fresh\CentrifugoBundle\Service\Credentials\CredentialsGenerator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class CentrifugoAnonymousController
{
    #[Route(path: '/centrifugo/credentials/anonymous', name: 'get_centrifugo_credentials_for_anonymous', methods: [Request::METHOD_GET])]
    public function __construct(private readonly CredentialsGenerator $credentialsGenerator)
    {
        $this->credentialsGenerator = $credentialsGenerator;
    }

    public function getJwtTokenForAnonymousAction(): JsonResponse
    {
        $token = $this->credentialsGenerator->generateJwtTokenForAnonymous();

        return new JsonResponse(['token' => $token], JsonResponse::HTTP_OK);
    }
}
```

## Authenticated User

If in your Symfony application you have a `User` entity, then it should implement the [`UserInterface`](https://github.com/symfony/security-core/blob/master/User/UserInterface.php) interface.

To allow user be authenticated in Centrifugo, you **have to implement interface** [`CentrifugoUserInterface`](./../../User/CentrifugoUserInterface.php).
It has two methods: `getCentrifugoSubject()`, `getCentrifugoUserInfo()`. Which return information needed for JWT token claims.

### Implement `CentrifugoUserInterface` for your User entity

```php
<?php
declare(strict_types=1);

namespace App\Entity;

use Fresh\CentrifugoBundle\User\CentrifugoUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements CentrifugoUserInterface, UserInterface
{
    // ... implement methods from UserInterface

    public function getCentrifugoSubject(): string
    {
        return $this->getUsername(); // or ->getId()
    }

    public function getCentrifugoUserInfo(): array
    {
        // User info is not required, you can return an empty array
        // return [];

        return [
            'username' => $this->getUsername(), // Or some additional info, if you wish
        ];
    }
}

```

If you want to use `meta` JWT claim of Centrifugo which allows proxy calls, then use [`CentrifugoUserMetaInterface`](./../../User/CentrifugoUserMetaInterface.php) instead. This interface extends the [`CentrifugoUserInterface`](./../../User/CentrifugoUserInterface.php) and adds additional method to implement `getCentrifugoUserMeta()`. 

```php
<?php
declare(strict_types=1);

namespace App\Entity;

use Fresh\CentrifugoBundle\User\CentrifugoUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements CentrifugoUserMetaInterface, UserInterface
{
    // ... implement methods from UserInterface

    public function getCentrifugoSubject(): string
    {
        return $this->getUsername(); // or ->getId()
    }

    public function getCentrifugoUserInfo(): array
    {
        // User info is not required, you can return an empty array
        // return [];

        return [
            'username' => $this->getUsername(), // Or some additional info, if you wish
        ];
    }
    
    public function getCentrifugoUserMeta(): array
    {
        return [
            'foo' => 'bar', // Some payload you want to add to `meta` claim.
        ];
    }
}

```


### Use `CredentialsGenerator` to receive a JWT token for authenticated user

```php
<?php
declare(strict_types=1);

namespace App\Controller;

use Fresh\CentrifugoBundle\Service\Credentials\CredentialsGenerator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Routing\Annotation\Route;

class CentrifugoCredentialsController
{
    #[Route(path: '/centrifugo/credentials/user', name: 'get_centrifugo_credentials_for_current_user', methods: [Request::METHOD_GET])]
    public function __construct(private readonly CredentialsGenerator $credentialsGenerator, private readonly TokenStorageInterface $tokenStorage)
    {
        $this->credentialsGenerator = $credentialsGenerator;
        $this->tokenStorage = $tokenStorage;
    }

    public function getJwtTokenForCurrentUserAction(): JsonResponse
    {
        /** @var Fresh\CentrifugoBundle\User\CentrifugoUserInterface $user */
        $user = $this->tokenStorage->getToken()->getUser();
        
        // $user should be an instance of Fresh\CentrifugoBundle\User\CentrifugoUserInterface
        $token = $this->credentialsGenerator->generateJwtTokenForUser($user);

        return new JsonResponse(['token' => $token], JsonResponse::HTTP_OK);
    }
}
```

## Private Channel

### Create own channel authenticator

This bundle provides possibility to register custom channel authenticators for private channels.
What you need is to create a service which implements [`ChannelAuthenticatorInterface`](./../../Service/ChannelAuthenticator/ChannelAuthenticatorInterface.php).

```php
<?php
declare(strict_types=1);

namespace App\Service\Centrifugo\ChannelAuthenticator;

use Fresh\CentrifugoBundle\Service\ChannelAuthenticator\ChannelAuthenticatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AdminsChannelAuthenticator implements ChannelAuthenticatorInterface
{
    public function __construct(private readonly AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    // This method is used to detect channels which are supported by this channel authenticator
    public function supports(string $channel, Request $request): bool
    {
        return 0 === \mb_strpos($channel, '$admins');
    }

    // This method is used to decide if current user is granted to access this private channel
    public function hasAccessToChannel(string $channel, Request $request): bool
    {
        return $this->authorizationChecker->isGranted('ROLE_ADMIN');
    }
}
```

### Use `PrivateChannelAuthenticator` in your controller

```php
<?php
declare(strict_types=1);

namespace App\Controller;

use Fresh\CentrifugoBundle\Service\ChannelAuthenticator\PrivateChannelAuthenticator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CentrifugoSubscribeController
{
    public function __construct(private readonly PrivateChannelAuthenticator $privateChannelAuthenticator)
    {
        $this->privateChannelAuthenticator = $privateChannelAuthenticator;
    }

    #[Route(path: '/centrifugo/subscribe', name: 'centrifugo_subscribe', methods: [Request::METHOD_POST])]
    public function centrifugoSubscribeAction(Request $request): JsonResponse
    {
        $data = $this->privateChannelAuthenticator->authChannelsForClientFromRequest($request);

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }
}
```

## More features

* [Back to index](./../../README.md "Back to index")
* [Examples of using Centrifugo service](./centrifugo_service_methods.md "Examples of using Centrifugo service")
* [Examples of using console commands](./console_commands.md "Examples of using console commands")
* [Customize bundle configuration](./configuration.md "Customize bundle configuration")
