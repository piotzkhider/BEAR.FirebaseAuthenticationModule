<?php

declare(strict_types=1);

namespace Piotzkhider\FirebaseAuthenticationModule\IDTokenExtractor;

use Aura\Web\Request;

interface IDTokenExtractorInterface
{
    public function supports(Request $request): bool;

    public function extract(Request $request): string;
}
