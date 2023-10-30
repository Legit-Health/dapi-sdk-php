<?php

namespace LegitHealth\Dapi\MediaAnalyzerResponse\Value;

final readonly class ValidityMetric
{
    public function __construct(public string $name, public bool $pass)
    {
    }
}
