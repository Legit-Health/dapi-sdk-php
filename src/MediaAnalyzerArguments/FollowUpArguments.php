<?php

namespace Legit\Dapi\MediaAnalyzerArguments;

use Legit\Dapi\MediaAnalyzerArguments\BodySite\BodySiteCode;
use Legit\Dapi\MediaAnalyzerArguments\Operator\Operator;
use Legit\Dapi\MediaAnalyzerArguments\Questionnaires\Questionnaires;
use Legit\Dapi\MediaAnalyzerArguments\Subject\Subject;
use Legit\Dapi\MediaAnalyzerArguments\PreviousMedia\PreviousMedia;

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
