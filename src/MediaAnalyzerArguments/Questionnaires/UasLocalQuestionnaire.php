<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires;

readonly class UasLocalQuestionnaire extends Questionnaire
{
    public function __construct(public int $itchiness, public int $hiveNumber)
    {
        $this->ensureIsInRange($itchiness, 0, 3, 'itchiness');
    }

    public static function getName(): string
    {
        return 'UAS_LOCAL';
    }

    public function toArray(): array
    {
        return [
            'itchiness' => $this->itchiness,
            'hiveNumber' => $this->hiveNumber
        ];
    }
}
