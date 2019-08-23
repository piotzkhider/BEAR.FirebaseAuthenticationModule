<?php

namespace Piotzkhider\FirebaseAuthenticationModule\IDTokenExtractor;

use Aura\Web\Request;

interface IDTokenExtractorInterface
{
    /**
     * @param Request $request
     *
     * @return bool
     */
    public function supports(Request $request): bool;

    /**
     * @param Request $request
     *
     * @return string
     */
    public function extract(Request $request): string;
}
