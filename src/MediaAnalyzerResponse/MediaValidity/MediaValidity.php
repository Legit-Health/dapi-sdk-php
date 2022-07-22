<?php

namespace LegitHealth\Dapi\MediaAnalyzerResponse\MediaValidity;

final class MediaValidity
{
    /** @var ValidityMetric[] */
    public readonly array $validityMetrics;

    /**
     * @param array<string,bool> $validityMetricsJson
     */
    public function __construct(
        public readonly bool $isValid,
        public readonly float $diqaScore,
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
