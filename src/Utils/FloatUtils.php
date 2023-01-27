<?php

namespace LegitHealth\Dapi\Utils;

class FloatUtils
{
    public static function floatOrNull(mixed $value): ?float
    {
        return \is_numeric($value) ? \floatval($value) : null;
    }
}
