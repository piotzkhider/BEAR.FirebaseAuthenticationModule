<?php

declare(strict_types=1);

namespace Piotzkhider\FirebaseAuthenticationModule\Extractor;

use Aura\Web\Request;
use PHPUnit\Framework\TestCase;
use Piotzkhider\FirebaseAuthenticationModule\Annotation\Extractors;
use Ray\AuraWebModule\AuraWebModule;
use Ray\Di\AbstractModule;
use Ray\Di\Injector;

class TokenExtractorResolverTest extends TestCase
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
                $this->bind()->annotatedWith(Extractors::class)->toInstance([
                    new FakeTokenExtractor(),
                ]);
                $this->bind(TokenExtractorResolver::class);
            }
        };
    }

    public function testResolve(): void
    {
        $injector = new Injector($this->module, dirname(__DIR__) . '/tmp');
        $request = $injector->getInstance(Request::class);
        $SUT = $injector->getInstance(TokenExtractorResolver::class);
        $result = $SUT->resolve($request);
        $this->assertInstanceOf(FakeTokenExtractor::class, $result);
    }
}
