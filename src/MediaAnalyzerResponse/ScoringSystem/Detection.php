<?php

namespace LegitHealth\Dapi\MediaAnalyzerResponse\ScoringSystem;

use LegitHealth\Dapi\MediaAnalyzerResponse\Point2d;

class Detection
{
    public function __construct(
        public readonly float $confidence,
        public readonly DetectionLabel $detectionLabel,
        public readonly Point2d $p1,
        public readonly Point2d $p2,
    ) {
    }

    public static function fromJson(array $json): self
    {
        return new self(
            $json['confidence'],
            DetectionLabel::from($json['label']),
            new Point2d($json['p1']['x'], $json['p1']['y']),
            new Point2d($json['p2']['x'], $json['p2']['y']),
        );
    }
}
