<?php

declare(strict_types=1);

namespace Piotzkhider\FirebaseAuthenticationModule\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("METHOD")
 */
class Auth
{
    /**
     * @var string
     */
    public $user;
}
