<?php

declare(strict_types=1);

namespace Piotzkhider\FirebaseAuthenticationModule\IDTokenExtractor;

use Aura\Web\Request;
use Piotzkhider\FirebaseAuthenticationModule\Annotation\Extractors;
use Piotzkhider\FirebaseAuthenticationModule\Exception\IDTokenNotFound;

class IDTokenExtractorResolver
{
    /**
     * @var IDTokenExtractorInterface[]
     */
    private $extractors;

    /**
     * @Extractors()
     *
     * @param IDTokenExtractorInterface[] $extractors
     */
    public function __construct(array $extractors)
    {
        $this->extractors = $extractors;
    }

    public function resolve(Request $request): IDTokenExtractorInterface
    {
        foreach ($this->extractors as $extractor) {
            if ($extractor->supports($request)) {
                return $extractor;
            }
        }

        throw new IDTokenNotFound();
    }
}
