<?php

declare(strict_types=1);

namespace Piotzkhider\FirebaseAuthenticationModule\Guard;

use Aura\Web\Request;
use BEAR\Resource\ResourceObject;
use Kreait\Firebase\Auth\UserRecord;
use Lcobucci\JWT\Token;
use Piotzkhider\FirebaseAuthenticationModule\Exception\AuthenticationException;

interface AuthenticatorInterface
{
    /**
     * @throws AuthenticationException
     */
    public function getCredentials(Request $request): Token;

    public function getUser(Token $token): UserRecord;

    /**
     * @throws AuthenticationException
     */
    public function onAuthenticationFailure(ResourceObject $ro, AuthenticationException $e): ResourceObject;
}
