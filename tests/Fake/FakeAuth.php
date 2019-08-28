<?php

declare(strict_types=1);

namespace Piotzkhider\FirebaseAuthenticationModule;

use Kreait\Firebase\Auth\UserRecord;
use Lcobucci\JWT\Claim\Basic;
use Lcobucci\JWT\Token;

class FakeAuth implements AuthInterface
{
    public function verifyIdToken(string $idToken): Token
    {
        unset($idToken);

        $claim = new Basic('sub', 'uid');

        return new Token([], ['sub' => $claim]);
    }

    public function getUser(string $uid): UserRecord
    {
        unset($uid);

        return new UserRecord();
    }
}
