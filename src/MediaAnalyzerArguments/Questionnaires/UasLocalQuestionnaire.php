<?php

namespace Legit\Dapi\MediaAnalyzerArguments\Questionnaires;

class UasLocalQuestionnaire extends Questionnaire
{
    public function __construct(public readonly int $itchiness, public readonly int $hiveNumber)
    {
        $this->ensureIsInRange($itchiness, 0, 3, 'itchiness');
    }

    public function getName(): string
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
