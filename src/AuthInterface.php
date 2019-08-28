<?php

declare(strict_types=1);

namespace Piotzkhider\FirebaseAuthenticationModule;

use Kreait\Firebase\Auth\UserRecord;
use Lcobucci\JWT\Token;
use Piotzkhider\FirebaseAuthenticationModule\Exception\InvalidToken;

interface AuthInterface
{
    /**
     * @throws InvalidToken
     */
    public function verifyIdToken(string $idToken): Token;

    public function getUser(string $uid): UserRecord;
}
