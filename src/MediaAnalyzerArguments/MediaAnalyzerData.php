<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments;

use LegitHealth\Dapi\MediaAnalyzerArguments\BodySite\BodySiteCode;
use LegitHealth\Dapi\MediaAnalyzerArguments\Operator\Operator;
use LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires\Questionnaires;
use LegitHealth\Dapi\MediaAnalyzerArguments\Subject\Subject;
use LegitHealth\Dapi\MediaAnalyzerArguments\PreviousMedia\PreviousMedia;
use LegitHealth\Dapi\MediaAnalyzerArguments\View\View;

abstract readonly class MediaAnalyzerData
{
    /**
     * @param string|string[] $content
     * @param string[] $scoringSystems
     * @param PreviousMedia[] $previousMedias
     */
    public function __construct(
        public string|array $content,
        public ?BodySiteCode $bodySiteCode = null,
        public ?string $pathologyCode = null,
        public array $previousMedias = [],
        public ?Operator $operator = null,
        public ?Subject $subject = null,
        public array $scoringSystems = [],
        public Questionnaires $questionnaires = new Questionnaires([]),
        public ?View $view = null
    ) {
    }

    public function toArray(): array
    {
        $previousMedias = [];
        foreach ($this->previousMedias as $previousMedia) {
            $previousMedias[] = [
                'date' => $previousMedia->date->format('c'),
                'content' => $previousMedia->base64
            ];
        }
        return [
            'type' => 'image',
            'modality' => 'clinical',
            'operator' => $this->operator?->value ?? null,
            'content' => $this->content,
            'bodySite' => $this->bodySiteCode === null ? null : $this->bodySiteCode->value,
            'knownConditionForThisImage' => [
                'conclusion' => $this->pathologyCode
            ],
            'previousMedia' => $previousMedias,
            'subject' => $this->subject?->toArray(),
            'scoringSystems' => $this->scoringSystems,
            'questionnaireResponse' => $this->questionnaires->toArray(),
            'view' => $this->view?->toArray() ?? null
        ];
    }
}
