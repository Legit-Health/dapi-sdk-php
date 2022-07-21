<?php

namespace Legit\Dapi\MediaAnalyzerResponse\MediaValidity;

final class ValidityMetric
{
    public function __construct(public readonly string $name, public readonly bool $pass)
    {
    }
}
