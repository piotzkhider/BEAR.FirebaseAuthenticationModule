<?php

declare(strict_types=1);

namespace Piotzkhider\FirebaseAuthenticationModule\Extractor;

use Aura\Web\Request;
use PHPUnit\Framework\TestCase;
use Ray\AuraWebModule\AuraWebModule;
use Ray\Di\AbstractModule;
use Ray\Di\Injector;

class AuthorizationHeaderTokenExtractorTest extends TestCase
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
                $this->install(new AuraWebModule());
            }
        };
    }

    protected function tearDown(): void
    {
        unset($_SERVER['HTTP_AUTHORIZATION']);
    }

    public function testSupports(): void
    {
        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer token';
        $request = (new Injector($this->module, dirname(__DIR__) . '/tmp'))->getInstance(Request::class);
        $SUT = new AuthorizationHeaderTokenExtractor();
        $result = $SUT->supports($request);
        $this->assertTrue($result);
    }

    public function testExtract(): void
    {
        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer token';
        $request = (new Injector($this->module, dirname(__DIR__) . '/tmp'))->getInstance(Request::class);
        $SUT = new AuthorizationHeaderTokenExtractor();
        $result = $SUT->extract($request);
        $this->assertSame('token', $result);
    }
}
