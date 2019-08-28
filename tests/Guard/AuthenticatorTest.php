<?php

declare(strict_types=1);

namespace Piotzkhider\FirebaseAuthenticationModule\Guard;

use Aura\Web\Request;
use Koriym\HttpConstants\ResponseHeader;
use Koriym\HttpConstants\StatusCode;
use Kreait\Firebase\Auth\UserRecord;
use Lcobucci\JWT\Token;
use PHPUnit\Framework\TestCase;
use Piotzkhider\FirebaseAuthenticationModule\Annotation\Extractors;
use Piotzkhider\FirebaseAuthenticationModule\AuthInterface;
use Piotzkhider\FirebaseAuthenticationModule\Exception\TokenNotFound;
use Piotzkhider\FirebaseAuthenticationModule\Extractor\FakeTokenExtractor;
use Piotzkhider\FirebaseAuthenticationModule\Extractor\TokenExtractorResolver;
use Piotzkhider\FirebaseAuthenticationModule\FakeAuth;
use Piotzkhider\FirebaseAuthenticationModule\FakeResourceObject;
use Ray\AuraWebModule\AuraWebModule;
use Ray\Di\AbstractModule;
use Ray\Di\Injector;

class AuthenticatorTest extends TestCase
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
                $this->bind(AuthInterface::class)->to(FakeAuth::class);
                $this->bind()->annotatedWith(Extractors::class)->toInstance([
                    new FakeTokenExtractor(),
                ]);
                $this->bind(TokenExtractorResolver::class);
                $this->bind(AuthenticatorInterface::class)->to(Authenticator::class);
            }
        };
    }

    public function testGetCredentials(): Token
    {
        $injector = new Injector($this->module, dirname(__DIR__) . '/tmp');
        $request = $injector->getInstance(Request::class);
        $SUT = $injector->getInstance(AuthenticatorInterface::class);
        $result = $SUT->getCredentials($request);
        $this->assertInstanceOf(Token::class, $result);

        return $result;
    }

    /**
     * @depends testGetCredentials
     */
    public function testGetUser(Token $token): void
    {
        $SUT = (new Injector($this->module, dirname(__DIR__) . '/tmp'))->getInstance(AuthenticatorInterface::class);
        $result = $SUT->getUser($token);
        $this->assertInstanceOf(UserRecord::class, $result);
    }

    public function testOnAuthenticationFailure(): void
    {
        $ro = new FakeResourceObject();
        $SUT = (new Injector($this->module, dirname(__DIR__) . '/tmp'))->getInstance(AuthenticatorInterface::class);
        $result = $SUT->onAuthenticationFailure($ro, new TokenNotFound('token not found'));
        $this->assertSame(StatusCode::UNAUTHORIZED, $result->code);
        $this->assertSame(
            'Bearer realm="token_required",error="token_not_found",error_description="token not found"',
            $result->headers[ResponseHeader::WWW_AUTHENTICATE]
        );
    }
}
