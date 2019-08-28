<?php

declare(strict_types=1);

namespace Piotzkhider\FirebaseAuthenticationModule\Guard;

use Aura\Web\Request;
use BEAR\Resource\ResourceObject;
use Koriym\HttpConstants\ResponseHeader;
use Koriym\HttpConstants\StatusCode;
use Kreait\Firebase\Auth\UserRecord;
use Lcobucci\JWT\Token;
use Piotzkhider\FirebaseAuthenticationModule\AuthInterface;
use Piotzkhider\FirebaseAuthenticationModule\Exception\AuthenticationException;
use Piotzkhider\FirebaseAuthenticationModule\Exception\InvalidToken;
use Piotzkhider\FirebaseAuthenticationModule\Exception\TokenNotFound;
use Piotzkhider\FirebaseAuthenticationModule\Extractor\TokenExtractorResolver;

class Authenticator implements AuthenticatorInterface
{
    /**
     * @var AuthInterface
     */
    private $auth;

    /**
     * @var TokenExtractorResolver
     */
    private $resolver;

    public function __construct(AuthInterface $auth, TokenExtractorResolver $resolver)
    {
        $this->auth = $auth;
        $this->resolver = $resolver;
    }

    public function getCredentials(Request $request): Token
    {
        $extractor = $this->resolver->resolve($request);
        $idToken = $extractor->extract($request);

        return $this->auth->verifyIdToken($idToken);
    }

    public function getUser(Token $token): UserRecord
    {
        $uidClaim = $token->getClaim('sub');

        return $this->auth->getUser($uidClaim);
    }

    public function onAuthenticationFailure(ResourceObject $ro, AuthenticationException $e): ResourceObject
    {
        if ($e instanceof TokenNotFound) {
            $ro->code = StatusCode::UNAUTHORIZED;
            $ro->headers[ResponseHeader::WWW_AUTHENTICATE] = sprintf(
                'Bearer realm="token_required",error="token_not_found",error_description="%s"',
                $e->getMessage()
            );

            return $ro;
        }

        if ($e instanceof InvalidToken) {
            $ro->code = StatusCode::UNAUTHORIZED;
            $ro->headers[ResponseHeader::WWW_AUTHENTICATE] = sprintf(
                'Bearer realm="token_required",error="invalid_token",error_description="%s"',
                $e->getMessage()
            );

            return $ro;
        }

        throw $e;
    }
}
