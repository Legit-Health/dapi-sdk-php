<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires;

readonly class SevenPCQuestionnaire extends Questionnaire
{
    public function __construct(
        public int $question1SevenPC,
        public int $question2SevenPC,
        public int $question3SevenPC,
        public int $question4SevenPC,
        public int $question5SevenPC,
        public int $question6SevenPC,
        public int $question7SevenPC
    ) {
        $this->ensureIsInRange($question1SevenPC, 0, 1, 'question1SevenPC');
        $this->ensureIsInRange($question2SevenPC, 0, 1, 'question2SevenPC');
        $this->ensureIsInRange($question3SevenPC, 0, 1, 'question3SevenPC');
        $this->ensureIsInRange($question4SevenPC, 0, 1, 'question4SevenPC');
        $this->ensureIsInRange($question5SevenPC, 0, 1, 'question5SevenPC');
        $this->ensureIsInRange($question6SevenPC, 0, 1, 'question6SevenPC');
        $this->ensureIsInRange($question7SevenPC, 0, 1, 'question7SevenPC');
    }

    public static function getName(): string
    {
        return '7PC';
    }

    public function toArray(): array
    {
        return [
            'question1SevenPC' => $this->question1SevenPC,
            'question2SevenPC' => $this->question2SevenPC,
            'question3SevenPC' => $this->question3SevenPC,
            'question4SevenPC' => $this->question4SevenPC,
            'question5SevenPC' => $this->question5SevenPC,
            'question6SevenPC' => $this->question6SevenPC,
            'question7SevenPC' => $this->question7SevenPC,
        ];
    }
}
