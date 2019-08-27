<?php

declare(strict_types=1);

namespace Piotzkhider\FirebaseAuthenticationModule\Guard;

use Aura\Web\Request;
use Firebase\Auth\Token\Exception\InvalidToken;
use Koriym\HttpConstants\ResponseHeader;
use Koriym\HttpConstants\StatusCode;
use Kreait\Firebase;
use Kreait\Firebase\Auth\UserRecord;
use Lcobucci\JWT\Token;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Piotzkhider\FirebaseAuthenticationModule\Exception\IDTokenNotFound;
use Piotzkhider\FirebaseAuthenticationModule\FakeResource;
use Piotzkhider\FirebaseAuthenticationModule\FirebaseAuthenticationModule;
use Piotzkhider\FirebaseAuthenticationModule\IDTokenExtractor\IDTokenExtractorResolver;
use Ray\Di\Injector;

class AuthenticatorTest extends TestCase
{
    /**
     * @var Authenticator
     */
    private $SUT;

    /**
     * @var Firebase|MockObject
     */
    private $firebase;

    /**
     * @var Firebase\Auth|MockObject
     */
    private $auth;

    protected function setUp(): void
    {
        $this->firebase = $this->createMock(Firebase::class);
        $this->auth = $this->createMock(Firebase\Auth::class);

        $injector = new Injector(new FirebaseAuthenticationModule(dirname(__DIR__) . '/dummy.json'), __DIR__ . '/tmp');
        $resolver = $injector->getInstance(IDTokenExtractorResolver::class);
        $this->SUT = new Authenticator($this->firebase, $resolver);
    }

    public function testInstance(): void
    {
        $instance = (new Injector(new FirebaseAuthenticationModule(dirname(__DIR__) . '/dummy.json'), __DIR__ . '/tmp'))->getInstance(AuthenticatorInterface::class);
        $this->assertInstanceOf(Authenticator::class, $instance);
    }

    public function testGetCredentials(): void
    {
        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer example_token';

        $this->firebase->expects($this->once())
            ->method('getAuth')
            ->willReturn($this->auth);

        $this->auth->expects($this->once())
            ->method('verifyIdToken')
            ->with('example_token')
            ->willReturn($token = new Token());

        $request = (new Injector(new FirebaseAuthenticationModule(dirname(__DIR__) . '/dummy.json'), __DIR__ . '/tmp'))->getInstance(Request::class);
        $result = $this->SUT->getCredentials($request);

        $this->assertSame($token, $result);
    }

    public function testGetCredentialsWithInvalidToken(): void
    {
        $this->expectException(\Piotzkhider\FirebaseAuthenticationModule\Exception\InvalidToken::class);

        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer example_token';

        $this->firebase->expects($this->once())
            ->method('getAuth')
            ->willReturn($this->auth);

        $this->auth->expects($this->once())
            ->method('verifyIdToken')
            ->with('example_token')
            ->willThrowException(new InvalidToken(new Token()));

        $request = (new Injector(new FirebaseAuthenticationModule(dirname(__DIR__) . '/dummy.json'), __DIR__ . '/tmp'))->getInstance(Request::class);
        $this->SUT->getCredentials($request);
    }

    public function testGetUser(): void
    {
        $this->firebase->expects($this->once())
            ->method('getAuth')
            ->willReturn($this->auth);

        $this->auth->expects($this->once())
            ->method('getUser')
            ->with('uuid')
            ->willReturn(new UserRecord());

        $token = $this->createMock(Token::class);
        $token->expects($this->once())
            ->method('getClaim')
            ->with('sub')
            ->willReturn('uuid');

        $result = $this->SUT->getUser($token);
        $this->assertInstanceOf(UserRecord::class, $result);
    }

    public function testOnAuthenticationFailureIDTokenNotFound(): void
    {
        $ro = new FakeResource();
        $result = $this->SUT->onAuthenticationFailure($ro, new IDTokenNotFound('message'));
        $this->assertSame($ro, $result);
        $this->assertSame(StatusCode::UNAUTHORIZED, $result->code);
        $this->assertSame(
            'Bearer realm="token_required",error="token_not_found",error_description="message"',
            $result->headers[ResponseHeader::WWW_AUTHENTICATE]
        );
    }

    public function testOnAuthenticationFailureInvalidToken(): void
    {
        $ro = new FakeResource();
        $result = $this->SUT->onAuthenticationFailure($ro, new \Piotzkhider\FirebaseAuthenticationModule\Exception\InvalidToken('message'));
        $this->assertSame($ro, $result);
        $this->assertSame(StatusCode::UNAUTHORIZED, $result->code);
        $this->assertSame(
            'Bearer realm="token_required",error="invalid_token",error_description="message"',
            $result->headers[ResponseHeader::WWW_AUTHENTICATE]
        );
    }
}
