<?php

declare(strict_types=1);

namespace Piotzkhider\FirebaseAuthenticationModule;

use Piotzkhider\FirebaseAuthenticationModule\Annotation\Authenticate;
use Piotzkhider\FirebaseAuthenticationModule\Annotation\Extractors;
use Piotzkhider\FirebaseAuthenticationModule\Guard\Authenticator;
use Piotzkhider\FirebaseAuthenticationModule\Guard\AuthenticatorInterface;
use Piotzkhider\FirebaseAuthenticationModule\IDTokenExtractor\AuthorizationHeaderIDTokenExtractor;
use Piotzkhider\FirebaseAuthenticationModule\IDTokenExtractor\IDTokenExtractorResolver;
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

        $this->bind()->annotatedWith(Extractors::class)->toInstance([
            new AuthorizationHeaderIDTokenExtractor(),
        ]);
        $this->bind(IDTokenExtractorResolver::class)->in(Scope::SINGLETON);
        $this->bind(AuthenticatorInterface::class)->to(Authenticator::class)->in(Scope::SINGLETON);
        $this->bindInterceptor(
            $this->matcher->any(),
            $this->matcher->annotatedWith(Authenticate::class),
            [AuthenticationInterceptor::class]
        );
    }
}
