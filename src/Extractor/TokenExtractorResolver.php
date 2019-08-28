<?php

declare(strict_types=1);

namespace Piotzkhider\FirebaseAuthenticationModule\Extractor;

use Aura\Web\Request;
use Piotzkhider\FirebaseAuthenticationModule\Annotation\Extractors;
use Piotzkhider\FirebaseAuthenticationModule\Exception\TokenNotFound;

class TokenExtractorResolver
{
    /**
     * @var TokenExtractorInterface[]
     */
    private $extractors;

    /**
     * @Extractors()
     *
     * @var TokenExtractorInterface[]
     */
    public function __construct(array $extractors)
    {
        $this->extractors = $extractors;
    }

    public function resolve(Request $request): TokenExtractorInterface
    {
        foreach ($this->extractors as $extractor) {
            if ($extractor->supports($request)) {
                return $extractor;
            }
        }

        throw new TokenNotFound();
    }
}
