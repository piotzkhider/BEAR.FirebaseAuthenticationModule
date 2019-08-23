<?php

declare(strict_types=1);

namespace Piotzkhider\FirebaseAuthenticationModule\Guard;

use Aura\Web\Request;
use BEAR\Resource\ResourceObject;
use Koriym\HttpConstants\ResponseHeader;
use Koriym\HttpConstants\StatusCode;
use Kreait\Firebase;
use Kreait\Firebase\Auth\UserRecord;
use Lcobucci\JWT\Token;
use Piotzkhider\FirebaseAuthenticationModule\Exception\AuthenticationException;
use Piotzkhider\FirebaseAuthenticationModule\Exception\IDTokenNotFound;
use Piotzkhider\FirebaseAuthenticationModule\Exception\InvalidToken;
use Piotzkhider\FirebaseAuthenticationModule\IDTokenExtractor\IDTokenExtractorResolver;

class Authenticator implements AuthenticatorInterface
{
    /**
     * @var Firebase
     */
    protected $firebase;

    /**
     * @var IDTokenExtractorResolver
     */
    private $resolver;

    public function __construct(Firebase $firebase, IDTokenExtractorResolver $resolver)
    {
        $this->firebase = $firebase;
        $this->resolver = $resolver;
    }

    public function getCredentials(Request $request): Token
    {
        $extractor = $this->resolver->resolve($request);
        $idToken = $extractor->extract($request);

        try {
            return $this->firebase->getAuth()->verifyIdToken($idToken);
        } catch (\Firebase\Auth\Token\Exception\InvalidToken $e) {
            throw new InvalidToken($e->getMessage());
        }
    }

    public function getUser(Token $token): UserRecord
    {
        $uidClaim = $token->getClaim('sub');

        return $this->firebase->getAuth()->getUser($uidClaim);
    }

    public function onAuthenticationFailure(ResourceObject $caller, AuthenticationException $e): ResourceObject
    {
        if ($e instanceof IDTokenNotFound) {
            $caller->code = StatusCode::UNAUTHORIZED;
            $caller->headers[ResponseHeader::WWW_AUTHENTICATE] = sprintf(
                'Bearer realm="token_required",error="token_not_found",error_description="%s"',
                $e->getMessage()
            );

            return $caller;
        }

        if ($e instanceof InvalidToken) {
            $caller->code = StatusCode::UNAUTHORIZED;
            $caller->headers[ResponseHeader::WWW_AUTHENTICATE] = sprintf(
                'Bearer realm="token_required",error="invalid_token",error_description="%s"',
                $e->getMessage()
            );

            return $caller;
        }

        throw $e;
    }
}
