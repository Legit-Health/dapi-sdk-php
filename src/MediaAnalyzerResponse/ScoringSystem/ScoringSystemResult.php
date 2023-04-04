<?php

namespace LegitHealth\Dapi\MediaAnalyzerResponse\ScoringSystem;

use LegitHealth\Dapi\MediaAnalyzerResponse\ExplainabilityMedia;

final class ScoringSystemResult
{
    /** @param array<string,FacetScore> $facetScores */
    public function __construct(
        public readonly string $scoringSystemCode,
        public readonly ScoringSystemScore $score,
        public readonly array $facetScores,
        public readonly ExplainabilityMedia $explainabilityMedia,
    ) {
    }

    public static function fromJson(
        string $scoringSystemCode,
        array $json
    ): self {
        $scoringSystemScore = new ScoringSystemScore(
            $json['grade']['category'] ?? null,
            $json['grade']['score']
        );

        $facetScores = [];
        foreach ($json['facets'] as $facetCode => $scoreJson) {
            $facetScores["$facetCode"] = new FacetScore(
                $facetCode,
                $scoreJson['value'],
                $scoreJson['intensity']
            );
        }

        return new self(
            $scoringSystemCode,
            $scoringSystemScore,
            $facetScores,
            ExplainabilityMedia::fromJson($json['explainabilityMedia'] ?? null)
        );
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
