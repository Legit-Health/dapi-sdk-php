<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires;

readonly class ScoradLocalQuestionnaire extends Questionnaire
{
    public function __construct(
        public float $surfaceValue,
        public int $erythema,
        public int $swelling,
        public int $crusting,
        public int $excoriation,
        public int $lichenification,
        public int $dryness,
        public int $itchinessScorad,
        public  int $sleeplessness
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
