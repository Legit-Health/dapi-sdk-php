<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires;

readonly class Ihs4LocalQuestionnaire extends Questionnaire
{
    public function __construct(
        public int $noduleNumber,
        public int $abscesseNumber,
        public int $drainingTunnelNumber
    ) {
    }

    public static function getName(): string
    {
        return 'IHS4_LOCAL';
    }

    public function toArray(): array
    {
        return [
            'noduleNumber' => $this->noduleNumber,
            'abscesseNumber' => $this->abscesseNumber,
            'drainingTunnelNumber' => $this->drainingTunnelNumber
        ];
    }
}
