<?php

declare(strict_types=1);

namespace Piotzkhider\FirebaseAuthenticationModule\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("METHOD")
 */
class Authenticate
{
    /**
     * @var string
     */
    public $user;
}
