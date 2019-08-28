<?php

declare(strict_types=1);

namespace Piotzkhider\FirebaseAuthenticationModule\Interceptor;

use Kreait\Firebase\Auth\UserRecord;
use PHPUnit\Framework\TestCase;
use Piotzkhider\FirebaseAuthenticationModule\Annotation\Authenticate;
use Piotzkhider\FirebaseAuthenticationModule\Fake\Guard\FakeAuthenticator;
use Piotzkhider\FirebaseAuthenticationModule\FakeAuthenticateConsumer;
use Piotzkhider\FirebaseAuthenticationModule\Guard\AuthenticatorInterface;
use Ray\AuraWebModule\AuraWebModule;
use Ray\Di\AbstractModule;
use Ray\Di\AssistedModule;
use Ray\Di\Injector;

class AuthenticationInterceptorTest extends TestCase
{
    /**
     * @var AbstractModule
     */
    private $module;

    protected function setUp(): void
    {
        $this->module = new class extends AbstractModule {
            protected function configure(): void
            {
                $this->install(new AssistedModule());
                $this->install(new AuraWebModule());
                $this->bind(AuthenticatorInterface::class)->to(FakeAuthenticator::class);
                $this->bind(FakeAuthenticateConsumer::class);
                $this->bindInterceptor(
                    $this->matcher->any(),
                    $this->matcher->annotatedWith(Authenticate::class),
                    [AuthenticationInterceptor::class]
                );
            }
        };
    }

    public function testInvoke(): void
    {
        /** @var FakeAuthenticateConsumer $consumer */
        $consumer = (new Injector($this->module, dirname(__DIR__) . '/tmp'))->getInstance(FakeAuthenticateConsumer::class);
        $result = $consumer->injected();
        $this->assertInstanceOf(UserRecord::class, $result);
        $this->assertSame('uid', $result->uid);
    }
}
