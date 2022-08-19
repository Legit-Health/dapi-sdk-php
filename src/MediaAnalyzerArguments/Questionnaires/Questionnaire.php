<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires;

use InvalidArgumentException;

abstract class Questionnaire
{
    abstract public function toArray(): array;
    abstract public static function getName(): string;

    /**
     * @param mixed ...$args
     * @return static
     */
    public static function new(mixed ...$args): static
    {
        return new static(...$args);
    }

    protected function ensureIsInRange(int|float $value, int $min, int $max, string $name): void
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
