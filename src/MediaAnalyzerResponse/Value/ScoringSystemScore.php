<?php

namespace LegitHealth\Dapi\MediaAnalyzerResponse\Value;

final readonly class ScoringSystemScore
{
    public function __construct(
        public ?string $category,
        public float $score
    ) {
    }
}
