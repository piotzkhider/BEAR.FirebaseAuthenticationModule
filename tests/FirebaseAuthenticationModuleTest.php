<?php

declare(strict_types=1);

namespace Piotzkhider\FirebaseAuthenticationModule;

use PHPUnit\Framework\TestCase;

class FirebaseAuthenticationModuleTest extends TestCase
{
    /**
     * @var FirebaseAuthenticationModule
     */
    protected $firebaseAuthenticationModule;

    protected function setUp(): void
    {
        $this->firebaseAuthenticationModule = new FirebaseAuthenticationModule();
    }

    public function testIsInstanceOfFirebaseAuthenticationModule(): void
    {
        $actual = $this->firebaseAuthenticationModule;
        $this->assertInstanceOf(FirebaseAuthenticationModule::class, $actual);
    }
}
