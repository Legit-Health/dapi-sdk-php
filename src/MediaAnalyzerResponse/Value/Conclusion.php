<?php

namespace LegitHealth\Dapi\MediaAnalyzerResponse\Value;

final readonly class Conclusion
{
    public function __construct(
        public string $pathologyCode,
        public float $probability,
        public ConclusionCode $conclusionCode
    ) {
    }

    public function isPossible(): bool
    {
        return $this->probability > 0;
    }
}
