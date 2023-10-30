<?php

namespace LegitHealth\Dapi\MediaAnalyzerResponse;

use LegitHealth\Dapi\MediaAnalyzerArguments\MediaAnalyzerArguments;
use LegitHealth\Dapi\MediaAnalyzerResponse\Value\{
    Conclusion,
    ConclusionCode,
    FailedMedia,
    MediaValidity,
    MetricsValue,
    Observation,
    OriginalMedia,
    PreliminaryFindingsValue
};

final readonly class DiagnosisSupportResponse
{
    /**
     * @param Conclusion[] $conclusions
     * @param Observation[] $observations
     */
    public function __construct(
        public PreliminaryFindingsValue $preliminaryFindings,
        public MetricsValue $metrics,
        public array $conclusions,
        public array $observations,
        public float $iaSeconds
    ) {
    }

    public static function createFromJson(MediaAnalyzerArguments $mediaAnalyzerArguments, array $json): self
    {
        $preliminaryFindings = new PreliminaryFindingsValue(
            $json['preliminaryFindings']['hasConditionSuspicion'],
            $json['preliminaryFindings']['isPreMalignantSuspicion'],
            $json['preliminaryFindings']['isMalignantSuspicion'] ?? null,
            $json['preliminaryFindings']['needsBiopsySuspicion'],
            $json['preliminaryFindings']['needsSpecialistsAttention'],
        );

        $metrics = new MetricsValue($json['metrics']['sensitivity'], $json['metrics']['specificity']);

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

        $observations = [];
        foreach ($json['imagingStudySeries'] as $key => $imagingStudySerie) {
            $conclusions = [];
            $apiConclusions = $imagingStudySerie['conclusions'] ?? [];

            foreach ($apiConclusions as $singleConclusion) {
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

            $observations[] = new Observation(
                $conclusions,
                new OriginalMedia(
                    $mediaAnalyzerArguments->data->content[$key],
                    $imagingStudySerie['detectedModality'],
                    new MediaValidity(
                        $imagingStudySerie['mediaValidity']['isValid'],
                        $imagingStudySerie['mediaValidity']['score'],
                        $imagingStudySerie['mediaValidity']['metrics']
                    )
                ),
                null,
                new MetricsValue($imagingStudySerie['metrics']['sensitivity'], $imagingStudySerie['metrics']['specificity']),
                new PreliminaryFindingsValue(
                    $imagingStudySerie['preliminaryFindings']['hasConditionSuspicion'],
                    $imagingStudySerie['preliminaryFindings']['isPreMalignantSuspicion'],
                    $imagingStudySerie['preliminaryFindings']['isMalignantSuspicion'] ?? null,
                    $imagingStudySerie['preliminaryFindings']['needsBiopsySuspicion'] ?? null,
                    $imagingStudySerie['preliminaryFindings']['needsSpecialistsAttention'],
                )
            );
        }

        return new self(
            $preliminaryFindings,
            $metrics,
            $conclusions,
            $observations,
            $iaSeconds
        );
    }

    /**
     * @return Conclusion[]
     */
    public function getPossibleConclusions(): array
    {
        return array_filter($this->conclusions, fn (Conclusion $conclusion) => $conclusion->isPossible());
    }

    /**
     * @return FailedMedia[]
     */
    public function getIndexOfFailedMedias(): array
    {
        $indexes = [];
        foreach ($this->observations as $index => $observation) {
            if (!$observation->originalMedia->mediaValidity->isValid) {
                $indexes[] = new FailedMedia(
                    $index,
                    $observation->originalMedia->mediaValidity->getFailedValidityMetric()
                );
            }
        }
        return $indexes;
    }
}
