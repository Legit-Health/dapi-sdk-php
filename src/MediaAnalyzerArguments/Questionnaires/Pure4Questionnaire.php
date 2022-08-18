<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires;

class Pure4Questionnaire extends Questionnaire
{
    public function __construct(
        public readonly int $question1_pure,
        public readonly int $question2_pure,
        public readonly int $question3_pure,
        public readonly int $question4_pure
    ) {
        $this->ensureIsInRange($question1_pure, 0, 1, 'question1_pure');
        $this->ensureIsInRange($question2_pure, 0, 1, 'question2_pure');
        $this->ensureIsInRange($question3_pure, 0, 1, 'question3_pure');
        $this->ensureIsInRange($question4_pure, 0, 1, 'question4_pure');
    }

    public static function getName(): string
    {
        return 'PURE4';
    }

    public function toArray(): array
    {
        return [
            'question1_pure' => $this->question1_pure,
            'question2_pure' => $this->question2_pure,
            'question3_pure' => $this->question3_pure,
            'question4_pure' => $this->question4_pure
        ];
    }
}
