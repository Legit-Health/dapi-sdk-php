<?php

namespace LegitHealth\Dapi\MediaAnalyzerResponse;

class ExplainabilityMediaMetrics
{
    public function __construct(
        public readonly ?float $pxToCm
    ) {
    }
}
