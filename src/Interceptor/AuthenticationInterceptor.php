<?php

declare(strict_types=1);

namespace Piotzkhider\FirebaseAuthenticationModule\Interceptor;

use Aura\Web\Request;
use BEAR\Resource\ResourceObject;
use Kreait\Firebase\Auth\UserRecord;
use Piotzkhider\FirebaseAuthenticationModule\Annotation\Authenticate;
use Piotzkhider\FirebaseAuthenticationModule\Exception\AuthenticationException;
use Piotzkhider\FirebaseAuthenticationModule\Exception\LogicException;
use Piotzkhider\FirebaseAuthenticationModule\Guard\AuthenticatorInterface;
use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;
use Ray\Di\MethodInvocationProvider;

class AuthenticationInterceptor implements MethodInterceptor
{
    /**
     * @var AuthenticatorInterface
     */
    private $guard;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var MethodInvocationProvider
     */
    private $invocationProvider;

    public function __construct(
        AuthenticatorInterface $guard,
        Request $request,
        MethodInvocationProvider $invocationProvider
    ) {
        $this->guard = $guard;
        $this->request = $request;
        $this->invocationProvider = $invocationProvider;
    }

    public function invoke(MethodInvocation $invocation)
    {
        $ro = $invocation->getThis();
        assert($ro instanceof ResourceObject);

        $annotation = $invocation->getMethod()->getAnnotation(Authenticate::class);
        assert($annotation instanceof Authenticate);

        try {
            $user = $this->authenticate();
            if ($annotation->user !== null) {
                $this->invocationProvider->set($invocation);
                $this->injectUser($invocation, $user);
            }

            return $invocation->proceed();
        } catch (AuthenticationException $e) {
            return $this->guard->onAuthenticationFailure($ro, $e);
        }
    }

    /**
     * @throws AuthenticationException
     */
    private function authenticate(): UserRecord
    {
        $token = $this->guard->getCredentials($this->request);

        return $this->guard->getUser($token);
    }

    private function injectUser(MethodInvocation $invocation, UserRecord $user): void
    {
        $method = $invocation->getMethod();
        $annotation = $method->getAnnotation(Authenticate::class);
        assert($annotation instanceof Authenticate);
        $parameters = $method->getParameters();
        $arguments = $invocation->getArguments()->getArrayCopy();

        foreach ($parameters as $parameter) {
            if ($parameter->getName() !== $annotation->user) {
                continue;
            }
            /** @var \ReflectionClass $hint */
            $hint = $parameter->getClass();
            if ($hint->getName() !== UserRecord::class) {
                throw new LogicException('User must be UserRecord.');
            }
            $pos = $parameter->getPosition();
            $arguments[$pos] = $user;
        }

        $invocation->getArguments()->exchangeArray($arguments);
    }
}
