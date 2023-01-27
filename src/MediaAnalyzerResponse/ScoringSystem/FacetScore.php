<?php

namespace LegitHealth\Dapi\MediaAnalyzerResponse\ScoringSystem;

final class FacetScore
{
    public function __construct(
        public readonly string $code,
        public readonly float $value,
        public readonly ?float $intensity
    ) {
    }
}
