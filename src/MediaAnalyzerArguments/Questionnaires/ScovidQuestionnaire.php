<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires;

readonly class ScovidQuestionnaire extends Questionnaire
{
    public function __construct(
        public int $pain,
        public int $itchinessScovid,
        public int $fever,
        public int $cough,
        public int $cephalea,
        public int $myalgiaorarthralgia,
        public int $malaise,
        public int $lossoftasteorolfactory,
        public int $shortnessofbreath,
        public int $otherskinproblems,
    ) {
        $this->ensureIsInRange($pain, 0, 10, 'pain');
        $this->ensureIsInRange($itchinessScovid, 0, 10, 'itchinessScovid');
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
            'itchinessScovid' => $this->itchinessScovid,
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
