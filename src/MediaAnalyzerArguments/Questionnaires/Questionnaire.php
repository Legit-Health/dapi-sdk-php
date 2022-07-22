<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires;

use InvalidArgumentException;

abstract class Questionnaire
{
    abstract public function toArray(): array;
    abstract public function getName(): string;

    protected function ensureIsInRange(int $value, int $min, int $max, string $name): void
    {
        if ($value >= $min && $value <= $max) {
            return;
        }
        throw new InvalidArgumentException(sprintf(
            '%s should be between %d and %d',
            $name,
            $min,
            $max
        ));
    }
}
