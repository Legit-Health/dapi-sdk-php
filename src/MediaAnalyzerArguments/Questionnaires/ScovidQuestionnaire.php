<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires;

class ScovidQuestionnaire extends Questionnaire
{
    public function __construct(
        public readonly int $pain,
        public readonly int $itchiness_scorad,
        public readonly int $fever,
        public readonly int $cough,
        public readonly int $cephalea,
        public readonly int $myalgiaorarthralgia,
        public readonly int $malaise,
        public readonly int $lossoftasteorolfactory,
        public readonly int $shortnessofbreath,
        public readonly int $otherskinproblems,
    ) {
        $this->ensureIsInRange($pain, 0, 10, 'pain');
        $this->ensureIsInRange($itchiness_scorad, 0, 10, 'itchiness_scorad');
        $this->ensureIsInRange($fever, 0, 3, 'fever');
        $this->ensureIsInRange($cough, 0, 3, 'cough');
        $this->ensureIsInRange($cephalea, 0, 3, 'cephalea');
        $this->ensureIsInRange($myalgiaorarthralgia, 0, 3, 'myalgiaorarthralgia');
        $this->ensureIsInRange($malaise, 0, 3, 'malaise');
        $this->ensureIsInRange($lossoftasteorolfactory, 0, 3, 'lossoftasteorolfactory');
        $this->ensureIsInRange($shortnessofbreath, 0, 3, 'shortnessofbreath');
        $this->ensureIsInRange($otherskinproblems, 0, 3, 'otherskinproblems');
    }

    public static function getName(): string
    {
        return 'SCOVID';
    }

    public function toArray(): array
    {
        return [
            'pain' => $this->pain,
            'itchiness_scorad' => $this->itchiness_scorad,
            'fever' => $this->fever,
            'cough' => $this->cough,
            'cephalea' => $this->cephalea,
            'myalgiaorarthralgia' => $this->myalgiaorarthralgia,
            'malaise' => $this->malaise,
            'lossoftasteorolfactory' => $this->lossoftasteorolfactory,
            'shortnessofbreath' => $this->shortnessofbreath,
            'otherskinproblems' => $this->otherskinproblems,
        ];
    }
}
