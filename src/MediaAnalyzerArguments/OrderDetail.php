<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments;

readonly class OrderDetail
{
    public function __construct(
        public bool $faceDetection = false
    ) {
    }

    public function toArray(): array
    {
        return [
            'faceDetection' => $this->faceDetection
        ];
    }
}
