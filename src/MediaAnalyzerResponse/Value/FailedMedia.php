<?php

namespace LegitHealth\Dapi\MediaAnalyzerResponse\Value;

final class FailedMedia
{
    public function __construct(
        public int $index,
        public ?ValidityMetric $failedMetric
    ) {
    }
}
