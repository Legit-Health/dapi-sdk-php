<?php

namespace LegitHealth\Dapi\MediaAnalyzerResponse\Value;

final readonly class Point2d
{
    public function __construct(
        public int $x,
        public int $y,
    ) {
    }
}
