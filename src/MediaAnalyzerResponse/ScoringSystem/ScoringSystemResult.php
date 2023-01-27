<?php

namespace LegitHealth\Dapi\MediaAnalyzerResponse\ScoringSystem;

final class ScoringSystemResult
{
    private ScoringSystemScore $score;
    /** @var array<string,FacetScore> */
    private array $facetScores;

    public function __construct(public readonly string $scoringSystemCode, private array $values)
    {
        $grade = $this->values['grade'];
        $this->score = new ScoringSystemScore(
            $grade['category'],
            $grade['score']
        );

        $this->facetScores = [];
        foreach ($this->values['facets'] as $facetCode => $scoreJson) {
            $this->facetScores["$facetCode"] = new FacetScore(
                $facetCode,
                $scoreJson['value'],
                $scoreJson['intensity']
            );
        }
    }

    public function getScore(): ScoringSystemScore
    {
        return $this->score;
    }

    public function getFacetScore(string $facetCode): FacetScore
    {
        return $this->facetScores[$facetCode];
    }

    /**
     * @return FacetScore[]
     */
    public function getFacetScores(): array
    {
        return array_values($this->facetScores);
    }
}
