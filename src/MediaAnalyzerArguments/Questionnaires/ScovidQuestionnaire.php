<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires;

class ScovidQuestionnaire extends Questionnaire
{
    public function __construct(
        public readonly int $pain,
        public readonly int $itchiness,
        public readonly int $fever,
        public readonly int $cough,
        public readonly int $cephalea,
        public readonly int $myalgiaOrArthralgia,
        public readonly int $malaise,
        public readonly int $lossOftAsteorolFactory,
        public readonly int $shortnessOfbreath,
        public readonly int $otherSkinProblems,
    ) {
        $this->ensureIsInRange($pain, 0, 10, 'pain');
        $this->ensureIsInRange($itchiness, 0, 10, 'itchiness');
        $this->ensureIsInRange($fever, 0, 3, 'fever');
        $this->ensureIsInRange($cough, 0, 3, 'cough');
        $this->ensureIsInRange($cephalea, 0, 3, 'cephalea');
        $this->ensureIsInRange($myalgiaOrArthralgia, 0, 3, 'myalgiaOrArthralgia');
        $this->ensureIsInRange($malaise, 0, 3, 'malaise');
        $this->ensureIsInRange($lossOftAsteorolFactory, 0, 3, 'lossOftAsteorolFactory');
        $this->ensureIsInRange($shortnessOfbreath, 0, 3, 'shortnessOfbreath');
        $this->ensureIsInRange($otherSkinProblems, 0, 3, 'otherSkinProblems');
    }

    public static function getName(): string
    {
        return 'SCOVID';
    }

    public function toArray(): array
    {
        return [
            'pain' => $this->pain,
            'itchiness_scorad' => $this->itchiness,
            'fever' => $this->fever,
            'cough' => $this->cough,
            'cephalea' => $this->cephalea,
            'myalgiaorarthralgia' => $this->myalgiaOrArthralgia,
            'malaise' => $this->malaise,
            'lossoftasteorolfactory' => $this->lossOftAsteorolFactory,
            'shortnessofbreath' => $this->shortnessOfbreath,
            'otherskinproblems' => $this->otherSkinProblems,
        ];
    }
}
