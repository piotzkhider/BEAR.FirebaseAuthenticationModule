<?php

declare(strict_types=1);

namespace Piotzkhider\FirebaseAuthenticationModule\Extractor;

use Aura\Web\Request;

interface TokenExtractorInterface
{
    public function supports(Request $request): bool;

    public function extract(Request $request): string;
}
