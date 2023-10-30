<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires;

readonly class ApulsiQuestionnaire extends Questionnaire
{
    public function __construct(
        public int $erythemaSurface,
        public int $painApusa,
        public int $odorApusa
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
