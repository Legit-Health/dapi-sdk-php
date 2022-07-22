<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires;

class PasiLocalQuestionnaire extends Questionnaire
{
    public function __construct(
        public readonly int $surface,
        public readonly int $erythema,
        public readonly int $induration,
        public readonly int $desquamation
    ) {
        $this->ensureIsInRange($surface, 0, 6, 'surface');
        $this->ensureIsInRange($erythema, 0, 4, 'erythema');
        $this->ensureIsInRange($induration, 0, 4, 'induration');
        $this->ensureIsInRange($desquamation, 0, 4, 'desquamation');
    }

    public function getName(): string
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
