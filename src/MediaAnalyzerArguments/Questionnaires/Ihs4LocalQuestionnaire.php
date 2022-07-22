<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires;

class Ihs4LocalQuestionnaire extends Questionnaire
{
    public function __construct(
        public readonly int $noduleNumber,
        public readonly int $abscesseNumber,
        public readonly int $drainingTunnelNumber
    ) {
    }

    public function getName(): string
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
