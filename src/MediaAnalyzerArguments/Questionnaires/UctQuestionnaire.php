<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires;

class UctQuestionnaire extends Questionnaire
{
    public function __construct(
        public readonly int $question1Uct,
        public readonly int $question2Uct,
        public readonly int $question3Uct,
        public readonly int $question4Uct,
    ) {
        $this->ensureIsInRange($question1Uct, 0, 4, 'question1Uct');
        $this->ensureIsInRange($question2Uct, 0, 4, 'question2Uct');
        $this->ensureIsInRange($question3Uct, 0, 4, 'question3Uct');
        $this->ensureIsInRange($question4Uct, 0, 4, 'question4Uct');
    }

    public function getName(): string
    {
        return 'UCT';
    }

    public function toArray(): array
    {
        return [
            'question1_uct' => $this->question1Uct,
            'question2_uct' => $this->question2Uct,
            'question3_uct' => $this->question3Uct,
            'question4_uct' => $this->question4Uct,
        ];
    }
}
