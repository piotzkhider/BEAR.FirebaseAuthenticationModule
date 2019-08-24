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

    public function invoke(MethodInvocation $invocation): ResourceObject
    {
        $caller = $invocation->getThis();
        if (! ($caller instanceof ResourceObject)) {
            throw new LogicException('Caller must be ResourceObject.');
        }

        try {
            $user = $this->authenticate();
            $this->injectAuthenticatedUser($invocation, $user);

            return $invocation->proceed();
        } catch (AuthenticationException $e) {
            return $this->guard->onAuthenticationFailure($caller, $e);
        }
    }

    /**
     * @return UserRecord
     * @throws AuthenticationException
     */
    private function authenticate(): UserRecord
    {
        $verifiedToken = $this->guard->getCredentials($this->request);

        return $this->guard->getUser($verifiedToken);
    }

    private function injectAuthenticatedUser(MethodInvocation $invocation, UserRecord $user): void
    {
        $method = $invocation->getMethod();
        $this->invocationProvider->set($invocation);
        $auth = $method->getAnnotation(Authenticate::class);
        if ($auth->user === null) {
            return;
        }
        $parameters = $method->getParameters();
        $arguments = $invocation->getArguments()->getArrayCopy();

        foreach ($parameters as $parameter) {
            if ($parameter->getName() !== $auth->user) {
                continue;
            }
            $hint = $parameter->getClass()->getName();
            if ($hint !== UserRecord::class) {
                throw new LogicException('User must be UserRecord.');
            }
            $pos = $parameter->getPosition();
            $arguments[$pos] = $user;
        }

        $invocation->getArguments()->exchangeArray($arguments);
    }
}
