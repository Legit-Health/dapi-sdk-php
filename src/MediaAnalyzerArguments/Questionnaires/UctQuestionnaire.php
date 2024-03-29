<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires;

readonly class UctQuestionnaire extends Questionnaire
{
    public function __construct(
        public int $question1Uct,
        public int $question2Uct,
        public int $question3Uct,
        public int $question4Uct,
    ) {
        $this->ensureIsInRange($question1Uct, 0, 4, 'question1Uct');
        $this->ensureIsInRange($question2Uct, 0, 4, 'question2Uct');
        $this->ensureIsInRange($question3Uct, 0, 4, 'question3Uct');
        $this->ensureIsInRange($question4Uct, 0, 4, 'question4Uct');
    }

    public static function getName(): string
    {
        return 'UCT';
    }

    public function toArray(): array
    {
        return [
            'question1Uct' => $this->question1Uct,
            'question2Uct' => $this->question2Uct,
            'question3Uct' => $this->question3Uct,
            'question4Uct' => $this->question4Uct,
        ];
    }
}
