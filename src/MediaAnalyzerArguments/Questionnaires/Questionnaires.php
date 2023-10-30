<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires;

readonly class Questionnaires
{
    /**
     * @param Questionnaire[] $questionnaires
     */
    public function __construct(
        public array $questionnaires
    ) {
    }

    public function toArray(): array
    {
        $json = [];
        foreach ($this->questionnaires as $questionnaire) {
            $json[$questionnaire::getName()] = $questionnaire->toArray();
        }
        return $json;
    }
}
