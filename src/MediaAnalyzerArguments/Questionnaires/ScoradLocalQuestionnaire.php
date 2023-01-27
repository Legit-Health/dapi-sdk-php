<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires;

class ScoradLocalQuestionnaire extends Questionnaire
{
    public function __construct(
        public readonly float $surfaceValue,
        public readonly int $erythema,
        public readonly int $swelling,
        public readonly int $crusting,
        public readonly int $excoriation,
        public readonly int $lichenification,
        public readonly int $dryness,
        public readonly int $itchinessScorad,
        public readonly int $sleeplessness
    ) {
        $this->ensureIsInRange($surfaceValue, 0, 100, 'surfaceValue');
        $this->ensureIsInRange($erythema, 0, 3, 'erythema');
        $this->ensureIsInRange($swelling, 0, 3, 'swelling');
        $this->ensureIsInRange($crusting, 0, 3, 'crusting');
        $this->ensureIsInRange($excoriation, 0, 3, 'excoriation');
        $this->ensureIsInRange($lichenification, 0, 3, 'lichenification');
        $this->ensureIsInRange($dryness, 0, 3, 'dryness');
        $this->ensureIsInRange($itchinessScorad, 0, 10, 'itchinessScorad');
        $this->ensureIsInRange($sleeplessness, 0, 10, 'sleeplessness');
    }

    public static function getName(): string
    {
        return 'SCORAD_LOCAL';
    }

    public function toArray(): array
    {
        return [
            'surfaceValue' => $this->surfaceValue,
            'itchinessScorad' => $this->itchinessScorad,
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
