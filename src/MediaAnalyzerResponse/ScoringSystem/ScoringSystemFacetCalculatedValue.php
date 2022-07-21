<?php

namespace Legit\Dapi\MediaAnalyzerResponse\ScoringSystem;

final class ScoringSystemFacetCalculatedValue
{
    public function __construct(public readonly ?string $value, public readonly ?float $intensity)
    {
    }
}
