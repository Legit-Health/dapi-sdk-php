<?php

namespace LegitHealth\Dapi\MediaAnalyzerResponse\Value;

final readonly class MediaValidity
{
    /** @var ValidityMetric[] */
    public array $validityMetrics;

    /**
     * @param array<string,bool> $validityMetricsJson
     */
    public function __construct(
        public bool $isValid,
        public float $diqaScore,
        array $validityMetricsJson
    ) {
        $validityMetrics = [];
        foreach ($validityMetricsJson as $name => $pass) {
            $validityMetrics[] = new ValidityMetric($name, $pass);
        }
        $this->validityMetrics = $validityMetrics;
    }

    public function getFailedValidityMetric(): ?ValidityMetric
    {
        foreach ($this->validityMetrics as $validityMetric) {
            if (!$validityMetric->pass) {
                return $validityMetric;
            }
        }
        return null;
    }
}
