<?php

declare(strict_types=1);

namespace Piotzkhider\FirebaseAuthenticationModule;

use Kreait\Firebase\Auth as FirebaseAuth;
use PHPUnit\Framework\TestCase;
use Piotzkhider\FirebaseAuthenticationModule\Extractor\TokenExtractorResolver;
use Piotzkhider\FirebaseAuthenticationModule\Guard\Authenticator;
use Piotzkhider\FirebaseAuthenticationModule\Guard\AuthenticatorInterface;
use Ray\Compiler\DiCompiler;
use Ray\Compiler\ScriptInjector;
use Ray\Di\Injector;

class FirebaseAuthenticationModuleTest extends TestCase
{
    public function testModule(): void
    {
        $injector = new Injector(new FirebaseAuthenticationModule(__DIR__ . '/dummy.json'), __DIR__ . '/tmp');

        $firebaseAuth = $injector->getInstance(FirebaseAuth::class);
        $this->assertInstanceOf(FirebaseAuth::class, $firebaseAuth);

        $auth = $injector->getInstance(AuthInterface::class);
        $this->assertInstanceOf(Auth::class, $auth);

        $resolver = $injector->getInstance(TokenExtractorResolver::class);
        $this->assertInstanceOf(TokenExtractorResolver::class, $resolver);

        $authenticator = $injector->getInstance(AuthenticatorInterface::class);
        $this->assertInstanceOf(Authenticator::class, $authenticator);
    }

    public function testCompile(): void
    {
        (new DiCompiler(new FirebaseAuthenticationModule(__DIR__ . '/dummy.json'), __DIR__ . '/tmp'))->compile();

        $injector = new ScriptInjector(__DIR__ . '/tmp');

        $firebaseAuth = $injector->getInstance(FirebaseAuth::class);
        $this->assertInstanceOf(FirebaseAuth::class, $firebaseAuth);

        $auth = $injector->getInstance(AuthInterface::class);
        $this->assertInstanceOf(Auth::class, $auth);

        $resolver = $injector->getInstance(TokenExtractorResolver::class);
        $this->assertInstanceOf(TokenExtractorResolver::class, $resolver);

        $authenticator = $injector->getInstance(AuthenticatorInterface::class);
        $this->assertInstanceOf(Authenticator::class, $authenticator);
    }
}
