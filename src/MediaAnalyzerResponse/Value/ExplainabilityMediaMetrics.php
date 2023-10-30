<?php

namespace LegitHealth\Dapi\MediaAnalyzerResponse\Value;

final readonly class ExplainabilityMediaMetrics
{
    public function __construct(
        public ?float $pxToCm
    ) {
    }
}
