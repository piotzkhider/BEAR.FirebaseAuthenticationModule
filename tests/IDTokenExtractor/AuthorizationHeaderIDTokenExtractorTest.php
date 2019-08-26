<?php

declare(strict_types=1);

namespace Piotzkhider\FirebaseAuthenticationModule\IDTokenExtractor;

use Aura\Web\Request;
use PHPUnit\Framework\TestCase;
use Ray\AuraWebModule\AuraWebModule;
use Ray\Di\Injector;

class AuthorizationHeaderIDTokenExtractorTest extends TestCase
{
    /**
     * @var AuthorizationHeaderIDTokenExtractor
     */
    private $SUT;

    protected function setUp(): void
    {
        $this->SUT = new AuthorizationHeaderIDTokenExtractor();
    }

    protected function tearDown(): void
    {
        unset($_SERVER['HTTP_AUTHORIZATION']);
    }

    public function testSupports(): void
    {
        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer example_token';
        $request = (new Injector(new AuraWebModule(), __DIR__ . '/tmp'))->getInstance(Request::class);

        $this->assertTrue($this->SUT->supports($request));
    }

    public function testSupportsNoToken(): void
    {
        $request = (new Injector(new AuraWebModule(), __DIR__ . '/tmp'))->getInstance(Request::class);

        $this->assertFalse($this->SUT->supports($request));
    }

    public function testSupportsWithoutPrefix(): void
    {
        $_SERVER['HTTP_AUTHORIZATION'] = 'example_token';
        $request = (new Injector(new AuraWebModule(), __DIR__ . '/tmp'))->getInstance(Request::class);

        $this->assertFalse($this->SUT->supports($request));
    }

    public function testSupportsInvalidToken(): void
    {
        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer example token with white space';
        $request = (new Injector(new AuraWebModule(), __DIR__ . '/tmp'))->getInstance(Request::class);

        $this->assertFalse($this->SUT->supports($request));
    }

    public function testExtract(): void
    {
        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer example_token';
        $request = (new Injector(new AuraWebModule(), __DIR__ . '/tmp'))->getInstance(Request::class);
        $token = $this->SUT->extract($request);

        $this->assertSame('example_token', $token);
    }

    public function testExtractWithLowercaseBearer(): void
    {
        $_SERVER['HTTP_AUTHORIZATION'] = 'bearer example_token';
        $request = (new Injector(new AuraWebModule(), __DIR__ . '/tmp'))->getInstance(Request::class);
        $token = $this->SUT->extract($request);

        $this->assertSame('example_token', $token);
    }
}
