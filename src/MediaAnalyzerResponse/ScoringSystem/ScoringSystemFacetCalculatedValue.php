<?php

namespace LegitHealth\Dapi\MediaAnalyzerResponse\ScoringSystem;

final class ScoringSystemFacetCalculatedValue
{
    public function __construct(public readonly string|null $value, public readonly null|float $intensity)
    {
    }
}
