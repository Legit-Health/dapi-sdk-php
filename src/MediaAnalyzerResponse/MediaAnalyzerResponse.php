<?php

namespace LegitHealth\Dapi\MediaAnalyzerResponse;

use LegitHealth\Dapi\MediaAnalyzerResponse\Value\{
    Conclusion,
    ConclusionCode,
    ExplainabilityMedia,
    MediaValidity,
    MetricsValue,
    PreliminaryFindingsValue,
    ScoringSystemResult,
    ValidityMetric
};

final readonly class MediaAnalyzerResponse
{
    /**
     * @param ScoringSystemResult[] $scoringSystemsResults
     * @param Conclusion[] $conclusions
     */
    public function __construct(
        public PreliminaryFindingsValue $preliminaryFindings,
        public string $modality,
        public MediaValidity $mediaValidity,
        public MetricsValue $metrics,
        public ?ExplainabilityMedia $explainabilityMedia,
        public array $scoringSystemsResults,
        public array $conclusions,
        public float $iaSeconds
    ) {
    }

    public static function createFromJson(array $json): self
    {
        $preliminaryFindings = new PreliminaryFindingsValue(
            $json['preliminaryFindings']['hasConditionSuspicion'],
            $json['preliminaryFindings']['isPreMalignantSuspicion'],
            $json['preliminaryFindings']['isMalignantSuspicion'] ?? null,
            $json['preliminaryFindings']['needsBiopsySuspicion'],
            $json['preliminaryFindings']['needsSpecialistsAttention'],
        );

        $modality = $json['detectedModality'];

        $isValid = $json['mediaValidity']['isValid'];
        $diqaScore = $json['mediaValidity']['score'];
        $mediaValidityMetrics = $json['mediaValidity']['metrics'];
        $mediaValidity = new MediaValidity($isValid, $diqaScore, $mediaValidityMetrics);

        $metrics = new MetricsValue($json['metrics']['sensitivity'], $json['metrics']['specificity']);
        $explainabilityMedia = ExplainabilityMedia::fromJson($json['explainabilityMedia']);

        $scoringSystemsResults = [];
        if (isset($json['evolution']['domains'])) {
            foreach ($json['evolution']['domains'] as $scoringSystemCode => $values) {
                $scoringSystemsResults[] = ScoringSystemResult::fromJson(
                    $scoringSystemCode,
                    $values
                );
            }
        }

        $conclusions = [];
        if (isset($json['conclusions'])) {
            foreach ($json['conclusions'] as $singleConclusion) {
                if (isset($singleConclusion['name'])) {
                    $conclusions[] = new Conclusion(
                        $singleConclusion['name'],
                        $singleConclusion['probability'],
                        new ConclusionCode(
                            $singleConclusion['code']['code'],
                            $singleConclusion['code']['codeSystem']
                        )
                    );
                }
            }
        }


        $iaSeconds = $json['time'];

        return new self(
            $preliminaryFindings,
            $modality,
            $mediaValidity,
            $metrics,
            $explainabilityMedia,
            $scoringSystemsResults,
            $conclusions,
            $iaSeconds
        );
    }

    public function getScoringSystemResult(string $scoringSystemCode): ?ScoringSystemResult
    {
        foreach ($this->scoringSystemsResults as $scoringSystemValues) {
            if ($scoringSystemValues->scoringSystemCode === $scoringSystemCode) {
                return $scoringSystemValues;
            }
        }
        return null;
    }

    /**
     * @return Conclusion[]
     */
    public function getPossibleConclusions(): array
    {
        return array_filter($this->conclusions, fn (Conclusion $conclusion) => $conclusion->isPossible());
    }

    public function getFailedValidityMetric(): ?ValidityMetric
    {
        return $this->mediaValidity->getFailedValidityMetric();
    }
}
