<?php

declare(strict_types=1);

namespace Piotzkhider\FirebaseAuthenticationModule;

use BEAR\Resource\ResourceObject;
use Kreait\Firebase\Auth as FirebaseAuth;
use Piotzkhider\FirebaseAuthenticationModule\Annotation\Authenticate;
use Piotzkhider\FirebaseAuthenticationModule\Annotation\Extractors;
use Piotzkhider\FirebaseAuthenticationModule\Extractor\AuthorizationHeaderTokenExtractor;
use Piotzkhider\FirebaseAuthenticationModule\Extractor\TokenExtractorResolver;
use Piotzkhider\FirebaseAuthenticationModule\Firebase\FirebaseAuthProvider;
use Piotzkhider\FirebaseAuthenticationModule\Guard\Authenticator;
use Piotzkhider\FirebaseAuthenticationModule\Guard\AuthenticatorInterface;
use Piotzkhider\FirebaseAuthenticationModule\Interceptor\AuthenticationInterceptor;
use Piotzkhider\FirebaseModule\FirebaseModule;
use Ray\AuraWebModule\AuraWebModule;
use Ray\Di\AbstractModule;
use Ray\Di\AssistedModule;
use Ray\Di\Scope;

class FirebaseAuthenticationModule extends AbstractModule
{
    /**
     * @var mixed
     */
    private $credentials;

    public function __construct($credentials, AbstractModule $module = null)
    {
        $this->credentials = $credentials;
        parent::__construct($module);
    }

    protected function configure(): void
    {
        $this->install(new AssistedModule());
        $this->install(new AuraWebModule());
        $this->install(new FirebaseModule($this->credentials));

        $this->bind(FirebaseAuth::class)->toProvider(FirebaseAuthProvider::class)->in(Scope::SINGLETON);
        $this->bind(AuthInterface::class)->to(Auth::class)->in(Scope::SINGLETON);
        $this->bind()->annotatedWith(Extractors::class)->toInstance([
            new AuthorizationHeaderTokenExtractor(),
        ]);
        $this->bind(TokenExtractorResolver::class)->in(Scope::SINGLETON);
        $this->bind(AuthenticatorInterface::class)->to(Authenticator::class)->in(Scope::SINGLETON);
        $this->bindInterceptor(
            $this->matcher->subclassesOf(ResourceObject::class),
            $this->matcher->annotatedWith(Authenticate::class),
            [AuthenticationInterceptor::class]
        );
    }
}
