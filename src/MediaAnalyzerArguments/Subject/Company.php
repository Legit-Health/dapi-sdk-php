<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments\Subject;

readonly class Company
{
    public function __construct(
        public ?string $id = null,
        public ?string $name = null
    ) {
    }
}
