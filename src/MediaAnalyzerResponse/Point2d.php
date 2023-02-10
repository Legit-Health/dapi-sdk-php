<?php

namespace LegitHealth\Dapi\MediaAnalyzerResponse;

class Point2d
{
    public function __construct(
        public readonly int $x,
        public readonly int $y,
    ) {
    }
}
