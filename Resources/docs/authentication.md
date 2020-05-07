🔝 [Back to index](./../../README.md "Back to index")

# Authentication with JWT token 🗝️️

### Anonymous

@todo

### Authenticated User

If in your Symfony application you have a `User` entity, then it implements the interface `Symfony\Component\Security\Core\User\UserInterface`.
To allow this user to be authenticated in Centrifugo, you have to implement also `Fresh\CentrifugoBundle\User\CentrifugoUserInterface`.
It has two methods: `getCentrifugoSubject`, `getCentrifugoUserInfo`, which return information needed for JWT token claims.

```php
<?php
declare(strict_types=1);

namespace App\Entity;

use Fresh\CentrifugoBundle\User\CentrifugoUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements CentrifugoUserInterface, UserInterface
{
    // ... implement methods from UserInterface

    /**
     * @return string
     */
    public function getCentrifugoSubject(): string
    {
        return $this->getUsername(); // or ->getId()
    }

    /**
     * @return array
     */
    public function getCentrifugoUserInfo(): array
    {
        // User info is not required, you can return an empty array
        // return [];

        return [
            'username' => $this->getUsername(), // Or some additional info, if you want
        ];
    }
}

```


```php
<?php
declare(strict_types=1);

namespace App\Controller;

use Fresh\CentrifugoBundle\Service\Credentials\CredentialsGenerator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CentrifugoCredentialsController
{
    /** @var CredentialsGenerator */
    private $credentialsGenerator;
    
    /** @var TokenStorageInterface */
    private $tokenStorage;

    /**
     * @param CredentialsGenerator $credentialsGenerator
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(CredentialsGenerator $credentialsGenerator, TokenStorageInterface $tokenStorage)
    {
        $this->credentialsGenerator = $credentialsGenerator;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return JsonResponse
     */
    public function getTokenAction(): JsonResponse
    {
        /** @var \Fresh\CentrifugoBundle\User\CentrifugoUserInterface $user */
        $user = $this->tokenStorage->getToken()->getUser();
        // $user should be instance of Fresh\CentrifugoBundle\User\CentrifugoUserInterface
        $token = $this->credentialsGenerator->generateJwtTokenForUser($user);

        return new JsonResponse(['token' => $token], JsonResponse::HTTP_OK);
    }
}
```

### Private Channel

@todo

## More features

* [Back to index](./../../README.md "Back to index")
* [Examples of using Centrifugo service](./centrifugo_service_methods.md "Examples of using Centrifugo service")
* [Examples of using console commands](./console_commands.md "Examples of using console commands")
