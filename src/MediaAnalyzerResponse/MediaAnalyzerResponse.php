<?php

namespace LegitHealth\Dapi\MediaAnalyzerResponse;

use LegitHealth\Dapi\MediaAnalyzerResponse\Conclusion\Conclusion;
use LegitHealth\Dapi\MediaAnalyzerResponse\MediaValidity\{MediaValidity, ValidityMetric};
use LegitHealth\Dapi\MediaAnalyzerResponse\ScoringSystem\ScoringSystemResult;

final class MediaAnalyzerResponse
{
    /**
     * @param ScoringSystemResult[] $scoringSystemsResults
     * @param Conclusion[] $conclusions
     */
    public function __construct(
        public readonly PreliminaryFindingsValue $preliminaryFindings,
        public readonly string $modality,
        public readonly MediaValidity $mediaValidity,
        public readonly MetricsValue $metricsValue,
        public readonly ?string $explainabilityMedia,
        public readonly array $scoringSystemsResults,
        public readonly array $conclusions,
        public readonly float $iaSeconds
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
        $explainabilityMediaContent = $json['explainabilityMedia']['content'];
        $explainabilityMedia = $explainabilityMediaContent === null || $explainabilityMediaContent === '' ? null : $explainabilityMediaContent;

        $scoringSystemsResults = [];
        if (isset($json['evolution']['domains'])) {
            foreach ($json['evolution']['domains'] as $scoringSystemCode => $values) {
                $scoringSystemsResults[] = new ScoringSystemResult(
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
                        $singleConclusion['code']['code'],
                        $singleConclusion['code']['codeSystem'],
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
