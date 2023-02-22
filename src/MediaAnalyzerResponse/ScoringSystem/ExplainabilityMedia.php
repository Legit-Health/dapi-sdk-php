<?php

namespace LegitHealth\Dapi\MediaAnalyzerResponse\ScoringSystem;

final class ExplainabilityMedia
{
    /**
     * @param string|null $content
     * @param Detection[] $detections
     */
    public function __construct(
        public readonly ?string $content,
        public readonly ?array $detections
    ) {
    }

    public static function fromJson(?array $json): self
    {
        if ($json === null) {
            return new self(null, null);
        }
        $content = $json['content'] ?? null;
        $detections = $json['detections'] ?? null;
        return new self(
            $content === null || $content === '' ? null : $content,
            $detections === null ?
                null :
                array_map(fn (array $detectionJson) => Detection::fromJson($detectionJson), $detections)
        );
    }
}