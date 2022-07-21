<?php

namespace Legit\Dapi\MediaAnalyzerResponse\Conclusion;

final class Conclusion
{
    public readonly ConclusionCode $conclusionCode;

    public function __construct(
        public readonly string $pathologyCode,
        public readonly float $probability,
        string $code,
        string $codeSystem
    ) {
        $this->conclusionCode = new ConclusionCode($code, $codeSystem);
    }

    public function isPossible(): bool
    {
        return $this->probability > 0;
    }
}
