<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires;

class ApulsiQuestionnaire extends Questionnaire
{
    public function __construct(
        public readonly int $erythema_surface,
        public readonly int $pain_apusa,
        public readonly int $odor_apusa
    ) {
        $this->ensureIsInRange($erythema_surface, 0, 6, 'erythema_surface');
        $this->ensureIsInRange($pain_apusa, 0, 1, 'pain_apusa');
        $this->ensureIsInRange($odor_apusa, 0, 1, 'odor_apusa');
    }

    public static function getName(): string
    {
        return 'APULSI';
    }

    public function toArray(): array
    {
        return [
            'erythema_surface' => $this->erythema_surface,
            'pain_apusa' => $this->pain_apusa,
            'odor_apusa' => $this->odor_apusa,
        ];
    }
}
