<?php

namespace LegitHealth\Dapi\MediaAnalyzerResponse\Value;

final readonly class ConclusionCode
{
    public function __construct(
        public string $code,
        public string $codeSystem
    ) {
    }
}
