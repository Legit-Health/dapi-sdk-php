<?php

namespace Legit\Dapi\MediaAnalyzerArguments\Questionnaires;

class ScoradLocalQuestionnaire extends Questionnaire
{
    public function __construct(
        public readonly int $surface,
        public readonly int $erythema,
        public readonly int $swelling,
        public readonly int $crusting,
        public readonly int $excoriation,
        public readonly int $lichenification,
        public readonly int $dryness,
        public readonly int $itchiness,
        public readonly int $sleeplessness
    ) {
        $this->ensureIsInRange($surface, 0, 100, 'surface');
        $this->ensureIsInRange($erythema, 0, 3, 'erythema');
        $this->ensureIsInRange($swelling, 0, 3, 'swelling');
        $this->ensureIsInRange($crusting, 0, 3, 'crusting');
        $this->ensureIsInRange($excoriation, 0, 3, 'excoriation');
        $this->ensureIsInRange($lichenification, 0, 3, 'lichenification');
        $this->ensureIsInRange($dryness, 0, 3, 'dryness');
        $this->ensureIsInRange($itchiness, 0, 10, 'itchiness');
        $this->ensureIsInRange($sleeplessness, 0, 10, 'sleeplessness');
    }

    public function getName(): string
    {
        return 'SCORAD_LOCAL';
    }

    public function toArray(): array
    {
        return [
            'surface_value' => $this->surface,
            'itchiness_scorad' => $this->itchiness,
            'sleeplessness' => $this->sleeplessness,
            'erythema' => $this->erythema,
            'swelling' => $this->swelling,
            'crusting' => $this->crusting,
            'excoriation' => $this->excoriation,
            'lichenification' => $this->lichenification,
            'dryness' => $this->dryness
        ];
    }
}
