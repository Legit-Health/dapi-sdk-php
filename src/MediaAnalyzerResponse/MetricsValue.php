<?php

namespace LegitHealth\Dapi\MediaAnalyzerResponse;

final class MetricsValue
{
    public function __construct(public readonly float $sensitivity, public readonly float $specificity)
    {
    }
}
