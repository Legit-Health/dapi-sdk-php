<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires;

class AscoradLocalQuestionnaire extends Questionnaire
{
    public function __construct(
        public readonly int $surface,
        public readonly int $itchiness,
        public readonly int $sleeplessness
    ) {
        $this->ensureIsInRange($surface, 0, 100, 'surface');
        $this->ensureIsInRange($itchiness, 0, 10, 'itchiness');
        $this->ensureIsInRange($sleeplessness, 0, 10, 'sleeplessness');
    }

    public static function getName(): string
    {
        return 'ASCORAD_LOCAL';
    }

    public function toArray(): array
    {
        return [
            'itchiness_scorad' => $this->itchiness,
            'surface_value' => $this->surface,
            'sleeplessness' => $this->sleeplessness,
        ];
    }
}
