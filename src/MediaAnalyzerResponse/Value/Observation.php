<?php

namespace LegitHealth\Dapi\MediaAnalyzerResponse\Value;

final readonly class Observation
{
    /**
     * @param Conclusion[] $conclusions
     */
    public function __construct(
        public array $conclusions,
        public OriginalMedia $originalMedia,
        public ExplainabilityMedia|null $explainabilityMedia,
        public MetricsValue $metrics,
        public PreliminaryFindingsValue $preliminaryFindings,
    ) {
    }
}
