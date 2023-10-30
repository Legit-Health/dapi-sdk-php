<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires;

readonly class AuasLocalQuestionnaire extends Questionnaire
{
    public function __construct(public int $itchiness)
    {
        $this->ensureIsInRange($itchiness, 0, 3, 'itchiness');
    }

    public static function getName(): string
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
