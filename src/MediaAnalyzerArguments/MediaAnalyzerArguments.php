<?php

namespace Legit\Dapi\MediaAnalyzerArguments;

use Legit\Dapi\MediaAnalyzerArguments\BodySite\BodySiteCode;
use Legit\Dapi\MediaAnalyzerArguments\Operator\Operator;
use Legit\Dapi\MediaAnalyzerArguments\Questionnaires\Questionnaires;
use Legit\Dapi\MediaAnalyzerArguments\Subject\Subject;
use Legit\Dapi\MediaAnalyzerArguments\PreviousMedia\PreviousMedia;

abstract class MediaAnalyzerArguments
{
    /**
     * @param string[] $scoringSystems
     * @param PreviousMedia[] $previousMedias
     */
    public function __construct(
        public readonly string $requestId,
        public readonly string $content,
        public readonly ?BodySiteCode $bodySiteCode = null,
        public readonly ?string $pathologyCode = null,
        public readonly array $previousMedias = [],
        public readonly ?Operator $operator = null,
        public readonly ?Subject $subject = null,
        public readonly array $scoringSystems = [],
        public readonly Questionnaires $questionnaires = new Questionnaires([])
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
            'requestId' => $this->requestId,
            'data' => [
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
            ]
        ];
    }
}
