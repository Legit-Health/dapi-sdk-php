<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires;

class AscoradLocalQuestionnaire extends Questionnaire
{
    public function __construct(
        public readonly float $surface_value,
        public readonly int $itchiness_scorad,
        public readonly int $sleeplessness
    ) {
        $this->ensureIsInRange($surface_value, 0, 100, 'surface_value');
        $this->ensureIsInRange($itchiness_scorad, 0, 10, 'itchiness_scorad');
        $this->ensureIsInRange($sleeplessness, 0, 10, 'sleeplessness');
    }

    public static function getName(): string
    {
        return 'ASCORAD_LOCAL';
    }

    public function toArray(): array
    {
        return [
            'itchiness_scorad' => $this->itchiness_scorad,
            'surface_value' => $this->surface_value,
            'sleeplessness' => $this->sleeplessness,
        ];
    }
}
