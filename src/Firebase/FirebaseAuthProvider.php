<?php

declare(strict_types=1);

namespace Piotzkhider\FirebaseAuthenticationModule\Firebase;

use Kreait\Firebase\Auth;
use Piotzkhider\FirebaseModule\FirebaseInject;
use Ray\Di\ProviderInterface;

class FirebaseAuthProvider implements ProviderInterface
{
    use FirebaseInject;

    public function get(): Auth
    {
        return $this->firebase->getAuth();
    }
}
