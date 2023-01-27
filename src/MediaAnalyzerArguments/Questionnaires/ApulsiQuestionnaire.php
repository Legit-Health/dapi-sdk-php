<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires;

class ApulsiQuestionnaire extends Questionnaire
{
    public function __construct(
        public readonly int $erythemaSurface,
        public readonly int $painApusa,
        public readonly int $odorApusa
    ) {
        $this->ensureIsInRange($erythemaSurface, 0, 6, 'erythemaSurface');
        $this->ensureIsInRange($painApusa, 0, 1, 'painApusa');
        $this->ensureIsInRange($odorApusa, 0, 1, 'odorApusa');
    }

    public static function getName(): string
    {
        return 'APULSI';
    }

    public function toArray(): array
    {
        return [
            'erythemaSurface' => $this->erythemaSurface,
            'painApusa' => $this->painApusa,
            'odorApusa' => $this->odorApusa,
        ];
    }
}
