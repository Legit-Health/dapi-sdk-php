<?php

namespace LegitHealth\Dapi\MediaAnalyzerResponse\Conclusion;

final class ConclusionCode
{
    public function __construct(
        public readonly string $code,
        public readonly string $codeSystem
    ) {
    }
}
