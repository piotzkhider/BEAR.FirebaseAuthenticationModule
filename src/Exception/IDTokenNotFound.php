<?php

declare(strict_types=1);

namespace Piotzkhider\FirebaseAuthenticationModule\Exception;

class IDTokenNotFound extends RuntimeException implements AuthenticationException
{
    /**
     * @var string
     */
    protected $message = 'IDToken not found in request';
}
