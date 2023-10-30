<?php

namespace LegitHealth\Dapi\MediaAnalyzerResponse\Value;

final readonly class PreliminaryFindingsValue
{
    public function __construct(
        public float $hasConditionSuspicion,
        public float $isPreMalignantSuspicion,
        public float|null $isMalignantSuspicion,
        public float|null $needsBiopsySuspicion,
        public float $needsSpecialistsAttention
    ) {
    }
}
