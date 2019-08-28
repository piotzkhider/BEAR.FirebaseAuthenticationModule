<?php

declare(strict_types=1);

namespace Piotzkhider\FirebaseAuthenticationModule\Extractor;

use Aura\Web\Request;

class FakeTokenExtractor implements TokenExtractorInterface
{
    public function supports(Request $request): bool
    {
        unset($request);

        return true;
    }

    public function extract(Request $request): string
    {
        unset($request);

        return 'token';
    }
}
