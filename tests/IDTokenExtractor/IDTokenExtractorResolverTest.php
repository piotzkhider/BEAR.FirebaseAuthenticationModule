<?php

declare(strict_types=1);

namespace Piotzkhider\FirebaseAuthenticationModule\IDTokenExtractor;

use Aura\Web\Request;
use PHPUnit\Framework\TestCase;
use Piotzkhider\FirebaseAuthenticationModule\FirebaseAuthenticationModule;
use Ray\Di\Injector;

class IDTokenExtractorResolverTest extends TestCase
{
    protected function tearDown(): void
    {
        unset($_SERVER['HTTP_AUTHORIZATION']);
    }

    public function testResolve(): void
    {
        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer example_token';
        $request = (new Injector(new FirebaseAuthenticationModule(dirname(__DIR__) . '/dummy.json'), dirname(__DIR__) . '/tmp'))->getInstance(Request::class);
        $SUT = new IDTokenExtractorResolver([
            new AuthorizationHeaderIDTokenExtractor(),
        ]);
        $result = $SUT->resolve($request);
        $this->assertInstanceOf(AuthorizationHeaderIDTokenExtractor::class, $result);
    }

    /**
     * @expectedException \Piotzkhider\FirebaseAuthenticationModule\Exception\IDTokenNotFound
     */
    public function testResolveWithoutExtractor(): void
    {
        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer example_token';
        $request = (new Injector(new FirebaseAuthenticationModule(dirname(__DIR__) . '/dummy.json'), dirname(__DIR__) . '/tmp'))->getInstance(Request::class);
        $SUT = new IDTokenExtractorResolver([]);
        $SUT->resolve($request);
    }
}
