<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires;

readonly class Pure4Questionnaire extends Questionnaire
{
    public function __construct(
        public int $question1Pure,
        public int $question2Pure,
        public int $question3Pure,
        public int $question4Pure
    ) {
        $this->ensureIsInRange($question1Pure, 0, 1, 'question1Pure');
        $this->ensureIsInRange($question2Pure, 0, 1, 'question2Pure');
        $this->ensureIsInRange($question3Pure, 0, 1, 'question3Pure');
        $this->ensureIsInRange($question4Pure, 0, 1, 'question4Pure');
    }

    public static function getName(): string
    {
        return 'PURE4';
    }

    public function toArray(): array
    {
        return [
            'question1Pure' => $this->question1Pure,
            'question2Pure' => $this->question2Pure,
            'question3Pure' => $this->question3Pure,
            'question4Pure' => $this->question4Pure
        ];
    }
}
