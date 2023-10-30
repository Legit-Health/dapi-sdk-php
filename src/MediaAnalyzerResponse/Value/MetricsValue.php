<?php

namespace LegitHealth\Dapi\MediaAnalyzerResponse\Value;

final readonly class MetricsValue
{
    public function __construct(public float $sensitivity, public float $specificity)
    {
    }
}
