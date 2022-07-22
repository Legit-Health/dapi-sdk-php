<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments\Subject;

class Company
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $name = null
    ) {
    }
}
