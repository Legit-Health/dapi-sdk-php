<?php

namespace LegitHealth\Dapi\MediaAnalyzerResponse\Value;

final class FacetScore
{
    public function __construct(
        public string $code,
        public float $value,
        public ?float $intensity
    ) {
    }
}
