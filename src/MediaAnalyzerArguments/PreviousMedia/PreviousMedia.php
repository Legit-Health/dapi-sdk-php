<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments\PreviousMedia;

use DateTimeInterface;

class PreviousMedia
{
    public function __construct(
        public readonly string $base64,
        public readonly DateTimeInterface $date
    ) {
    }
}
