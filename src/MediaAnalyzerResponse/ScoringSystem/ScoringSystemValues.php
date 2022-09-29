<?php

namespace LegitHealth\Dapi\MediaAnalyzerResponse\ScoringSystem;

class ScoringSystemValues
{
    private ScoringSystemScore $score;

    public function __construct(public readonly string $scoringSystemCode, private array $values)
    {
        $calculatedValue = $this->values['grade']['float'] ?? null;
        $this->score = new ScoringSystemScore(
            $this->values['grade']['category'] ?? null,
            is_numeric($calculatedValue) ? \floatval($calculatedValue) : null,
            $this->values['questionnaire'] ?? null,
        );
    }

    public function getScore(): ScoringSystemScore
    {
        return $this->score;
    }

    public function getFacetCalculatedValue(string $facetCode): ScoringSystemFacetCalculatedValue
    {
        $calculatedValue = $this->values['facets'][$facetCode]['value'] ?? null;
        return new ScoringSystemFacetCalculatedValue(
            $this->values['facets'][$facetCode]['baseN'] ?? null,
            is_numeric($calculatedValue) ? \floatval($calculatedValue) : null
        );
    }

    public function getFacets(): array
    {
        $facets = [];
        foreach (array_keys($this->values['facets']) as $facetCode) {
            $scoringSystemFacetCalculatedValue = $this->getFacetCalculatedValue($facetCode);
            $facets[] =  [
                'facet' => $facetCode,
                'value' => $scoringSystemFacetCalculatedValue->value,
                'intensity' => $scoringSystemFacetCalculatedValue->intensity
            ];
        }
        return $facets;
    }
}
