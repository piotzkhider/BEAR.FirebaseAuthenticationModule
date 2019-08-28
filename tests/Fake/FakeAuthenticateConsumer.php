<?php

declare(strict_types=1);

namespace Piotzkhider\FirebaseAuthenticationModule;

use BEAR\Resource\ResourceObject;
use Kreait\Firebase\Auth\UserRecord;
use Piotzkhider\FirebaseAuthenticationModule\Annotation\Authenticate;

class FakeAuthenticateConsumer extends ResourceObject
{
    /**
     * @Authenticate(user="user")
     */
    public function injected(UserRecord $user = null)
    {
        return $user;
    }
}
