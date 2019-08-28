<?php

declare(strict_types=1);

namespace Piotzkhider\FirebaseAuthenticationModule\Fake\Guard;

use Aura\Web\Request;
use BEAR\Resource\ResourceObject;
use Kreait\Firebase\Auth\UserRecord;
use Lcobucci\JWT\Token;
use Piotzkhider\FirebaseAuthenticationModule\Exception\AuthenticationException;
use Piotzkhider\FirebaseAuthenticationModule\Guard\AuthenticatorInterface;

class FakeAuthenticator implements AuthenticatorInterface
{
    public function getCredentials(Request $request): Token
    {
        unset($request);

        return new Token();
    }

    public function getUser(Token $token): UserRecord
    {
        unset($token);

        $user = new UserRecord();
        $user->uid = 'uid';

        return $user;
    }

    public function onAuthenticationFailure(ResourceObject $ro, AuthenticationException $e): ResourceObject
    {
        unset($e);

        return $ro;
    }
}
