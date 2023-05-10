<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments;

class OrderDetail
{
    public function __construct(
        public readonly bool $faceDetection = false
    ) {
    }

    public function toArray(): array
    {
        return [
            'faceDetection' => $this->faceDetection
        ];
    }
}
