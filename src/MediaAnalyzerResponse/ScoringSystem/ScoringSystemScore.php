<?php

namespace LegitHealth\Dapi\MediaAnalyzerResponse\ScoringSystem;

final class ScoringSystemScore
{
    public function __construct(
        public readonly ?string $category = null,
        public readonly ?float $calculatedScore = null,
        public readonly ?float $questionnaireScore = null
    ) {
    }
}
