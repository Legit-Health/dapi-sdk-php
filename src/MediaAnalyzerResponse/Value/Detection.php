<?php

namespace LegitHealth\Dapi\MediaAnalyzerResponse\Value;

final readonly class Detection
{
    public function __construct(
        public float $confidence,
        public DetectionLabel $detectionLabel,
        public Point2d $p1,
        public Point2d $p2,
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
