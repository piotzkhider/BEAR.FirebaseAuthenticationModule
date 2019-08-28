<?php

declare(strict_types=1);

namespace Piotzkhider\FirebaseAuthenticationModule;

use Kreait\Firebase\Auth as FirebaseAuth;
use Kreait\Firebase\Auth\UserRecord;
use Lcobucci\JWT\Token;
use Piotzkhider\FirebaseAuthenticationModule\Exception\InvalidToken;

class Auth implements AuthInterface
{
    /**
     * @var FirebaseAuth
     */
    private $auth;

    public function __construct(FirebaseAuth $auth)
    {
        $this->auth = $auth;
    }

    public function verifyIdToken(string $idToken): Token
    {
        try {
            return $this->auth->verifyIdToken($idToken);
        } catch (\Firebase\Auth\Token\Exception\InvalidToken $e) {
            throw new InvalidToken($e->getMessage());
        }
    }

    public function getUser(string $uid): UserRecord
    {
        return $this->auth->getUser($uid);
    }
}
