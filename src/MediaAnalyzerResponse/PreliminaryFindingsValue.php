<?php

namespace LegitHealth\Dapi\MediaAnalyzerResponse;

final class PreliminaryFindingsValue
{
    public function __construct(
        public readonly float $hasConditionSuspicion,
        public readonly float $isPreMalignantSuspicion,
        public readonly float $needsBiopsySuspicion,
        public readonly float $needsSpecialistsAttention
    ) {
    }
}
