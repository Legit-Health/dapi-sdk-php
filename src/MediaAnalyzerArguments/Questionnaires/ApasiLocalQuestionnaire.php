<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires;

class ApasiLocalQuestionnaire extends Questionnaire
{
    public function __construct(public readonly int $surface)
    {
        $this->ensureIsInRange($surface, 0, 6, 'surface');
    }

    public function getName(): string
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
