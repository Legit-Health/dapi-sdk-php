<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments\PreviousMedia;

use DateTimeInterface;

readonly class PreviousMedia
{
    public function __construct(
        public string $base64,
        public DateTimeInterface $date
    ) {
    }
}
