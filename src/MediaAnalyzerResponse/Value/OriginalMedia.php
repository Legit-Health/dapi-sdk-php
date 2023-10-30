<?php

namespace LegitHealth\Dapi\MediaAnalyzerResponse\Value;

final readonly class OriginalMedia
{
    public function __construct(
        public string $content,
        public string $detectedModality,
        public MediaValidity $mediaValidity
    ) {
    }
}
