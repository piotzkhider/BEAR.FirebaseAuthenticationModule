<?php

namespace Piotzkhider\FirebaseAuthenticationModule\Guard;

use Aura\Web\Request;
use BEAR\Resource\ResourceObject;
use Kreait\Firebase\Auth\UserRecord;
use Lcobucci\JWT\Token;
use Piotzkhider\FirebaseAuthenticationModule\Exception\AuthenticationException;

interface AuthenticatorInterface
{
    /**
     * @param Request $request
     *
     * @return Token
     * @throws AuthenticationException
     */
    public function getCredentials(Request $request): Token;

    /**
     * @param Token $token
     *
     * @return UserRecord
     */
    public function getUser(Token $token): UserRecord;

    /**
     * @param ResourceObject          $caller
     * @param AuthenticationException $e
     *
     * @return ResourceObject
     * @throws AuthenticationException
     */
    public function onAuthenticationFailure(ResourceObject $caller, AuthenticationException $e): ResourceObject;
}

