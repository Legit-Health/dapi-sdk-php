<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires;

readonly class PasiLocalQuestionnaire extends Questionnaire
{
    public function __construct(
        public int $surface,
        public int $erythema,
        public int $induration,
        public int $desquamation
    ) {
        $this->ensureIsInRange($surface, 0, 6, 'surface');
        $this->ensureIsInRange($erythema, 0, 4, 'erythema');
        $this->ensureIsInRange($induration, 0, 4, 'induration');
        $this->ensureIsInRange($desquamation, 0, 4, 'desquamation');
    }

    public static function getName(): string
    {
        return 'PASI_LOCAL';
    }

    public function toArray(): array
    {
        return [
            'surface' => $this->surface,
            'erythema' => $this->erythema,
            'induration' => $this->induration,
            'desquamation' => $this->desquamation,
        ];
    }
}
