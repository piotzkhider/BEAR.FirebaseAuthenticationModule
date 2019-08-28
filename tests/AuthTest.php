<?php

declare(strict_types=1);

namespace Piotzkhider\FirebaseAuthenticationModule;

use Kreait\Firebase\Auth\UserRecord;
use Lcobucci\JWT\Token;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ray\Di\AbstractModule;
use Ray\Di\Injector;

class AuthTest extends TestCase
{
    /**
     * @var AbstractModule
     */
    private $module;

    /**
     * @var MockObject
     */
    private $auth;

    protected function setUp(): void
    {
        $this->auth = $this->createMock(\Kreait\Firebase\Auth::class);

        $this->module = new class($this->auth) extends AbstractModule {
            private $auth;

            public function __construct($auth, AbstractModule $module = null)
            {
                $this->auth = $auth;
                parent::__construct($module);
            }

            protected function configure(): void
            {
                $this->bind(\Kreait\Firebase\Auth::class)->toInstance($this->auth);
                $this->bind(AuthInterface::class)->to(Auth::class);
            }
        };
    }

    public function testVerifyIdToken(): void
    {
        $this->auth->expects($this->once())
            ->method('verifyIdToken')
            ->with('token')
            ->willReturn($token = new Token());

        $SUT = (new Injector($this->module, dirname(__DIR__) . '/tmp'))->getInstance(AuthInterface::class);
        $result = $SUT->verifyIdToken('token');
        $this->assertSame($token, $result);
    }

    public function testGetUser(): void
    {
        $this->auth->expects($this->once())
            ->method('getUser')
            ->with('uid')
            ->willReturn($user = new UserRecord());

        $SUT = (new Injector($this->module, dirname(__DIR__) . '/tmp'))->getInstance(AuthInterface::class);
        $result = $SUT->getUser('uid');
        $this->assertSame($user, $result);
    }
}
