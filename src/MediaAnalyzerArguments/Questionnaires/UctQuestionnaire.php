<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires;

class UctQuestionnaire extends Questionnaire
{
    public function __construct(
        public readonly int $question1_uct,
        public readonly int $question2_uct,
        public readonly int $question3_uct,
        public readonly int $question4_uct,
    ) {
        $this->ensureIsInRange($question1_uct, 0, 4, 'question1_uct');
        $this->ensureIsInRange($question2_uct, 0, 4, 'question2_uct');
        $this->ensureIsInRange($question3_uct, 0, 4, 'question3_uct');
        $this->ensureIsInRange($question4_uct, 0, 4, 'question4_uct');
    }

    public static function getName(): string
    {
        return 'UCT';
    }

    public function toArray(): array
    {
        return [
            'question1_uct' => $this->question1_uct,
            'question2_uct' => $this->question2_uct,
            'question3_uct' => $this->question3_uct,
            'question4_uct' => $this->question4_uct,
        ];
    }
}
