<?php

declare(strict_types=1);

namespace Piotzkhider\FirebaseAuthenticationModule\Exception;

class TokenNotFound extends RuntimeException implements AuthenticationException
{
    /**
     * @var string
     */
    protected $message = 'token not found';
}
