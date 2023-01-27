<?php

namespace LegitHealth\Dapi\MediaAnalyzerResponse\ScoringSystem;

final class ScoringSystemScore
{
    public function __construct(
        public readonly string $category,
        public readonly float $score
    ) {
    }
}
