<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires;

readonly class ApasiLocalQuestionnaire extends Questionnaire
{
    public function __construct(public int $surface)
    {
        $this->ensureIsInRange($surface, 0, 6, 'surface');
    }

    public static function getName(): string
    {
        return 'APASI_LOCAL';
    }

    public function toArray(): array
    {
        return [
            'surface' => $this->surface
        ];
    }
}
