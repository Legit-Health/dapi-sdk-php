<?php

namespace Legit\Dapi\MediaAnalyzerArguments\Questionnaires;

class Pure4Questionnaire extends Questionnaire
{
    public function __construct(
        public readonly int $question1,
        public readonly int $question2,
        public readonly int $question3,
        public readonly int $question4
    ) {
        $this->ensureIsInRange($question1, 0, 1, 'question1');
        $this->ensureIsInRange($question2, 0, 1, 'question2');
        $this->ensureIsInRange($question3, 0, 1, 'question3');
        $this->ensureIsInRange($question4, 0, 1, 'question4');
    }

    public function getName(): string
    {
        return 'PURE4';
    }

    public function toArray(): array
    {
        return [
            'question1_pure' => $this->question1,
            'question2_pure' => $this->question2,
            'question3_pure' => $this->question3,
            'question4_pure' => $this->question4
        ];
    }
}
