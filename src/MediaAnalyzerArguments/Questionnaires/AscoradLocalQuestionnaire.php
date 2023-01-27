<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires;

class AscoradLocalQuestionnaire extends Questionnaire
{
    public function __construct(
        public readonly float $surfaceValue,
        public readonly int $itchinessScorad,
        public readonly int $sleeplessness
    ) {
        $this->ensureIsInRange($surfaceValue, 0, 100, 'surfaceValue');
        $this->ensureIsInRange($itchinessScorad, 0, 10, 'itchinessScorad');
        $this->ensureIsInRange($sleeplessness, 0, 10, 'sleeplessness');
    }

    public static function getName(): string
    {
        return 'ASCORAD_LOCAL';
    }

    public function toArray(): array
    {
        return [
            'itchinessScorad' => $this->itchinessScorad,
            'surfaceValue' => $this->surfaceValue,
            'sleeplessness' => $this->sleeplessness,
        ];
    }
}
