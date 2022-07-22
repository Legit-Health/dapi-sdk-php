<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires;

class AuasLocalQuestionnaire extends Questionnaire
{
    public function __construct(public readonly int $itchiness)
    {
        $this->ensureIsInRange($itchiness, 0, 3, 'itchiness');
    }

    public function getName(): string
    {
        return 'AUAS_LOCAL';
    }

    public function toArray(): array
    {
        return [
            'itchiness' => $this->itchiness
        ];
    }
}
