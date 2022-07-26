<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments;

use LegitHealth\Dapi\MediaAnalyzerArguments\BodySite\BodySiteCode;
use LegitHealth\Dapi\MediaAnalyzerArguments\Operator\Operator;
use LegitHealth\Dapi\MediaAnalyzerArguments\Questionnaires\Questionnaires;
use LegitHealth\Dapi\MediaAnalyzerArguments\Subject\Subject;
use LegitHealth\Dapi\MediaAnalyzerArguments\PreviousMedia\PreviousMedia;

final class FollowUpArguments extends MediaAnalyzerArguments
{
    /**
     * @param string[] $scoringSystems
     * @param PreviousMedia[] $previousMedias
     */
    public function __construct(
        string $requestId,
        string $content,
        string $pathologyCode,
        ?BodySiteCode $bodySiteCode = null,
        array $previousMedias = [],
        ?Operator $operator = null,
        ?Subject $subject = null,
        array $scoringSystems = [],
        Questionnaires $questionnaires = new Questionnaires([])
    ) {
        parent::__construct(
            requestId: $requestId,
            content: $content,
            bodySiteCode: $bodySiteCode,
            pathologyCode: $pathologyCode,
            previousMedias: $previousMedias,
            operator: $operator,
            subject: $subject,
            scoringSystems: $scoringSystems,
            questionnaires: $questionnaires
        );
    }
}
