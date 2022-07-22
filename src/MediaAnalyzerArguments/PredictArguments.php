<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments;

use LegitHealth\Dapi\MediaAnalyzerArguments\BodySite\BodySiteCode;
use LegitHealth\Dapi\MediaAnalyzerArguments\Operator\Operator;
use LegitHealth\Dapi\MediaAnalyzerArguments\Subject\Subject;

final class PredictArguments extends MediaAnalyzerArguments
{
    public function __construct(
        string $requestId,
        string $content,
        ?BodySiteCode $bodySiteCode = null,
        ?Operator $operator = null,
        ?Subject $subject = null
    ) {
        parent::__construct(
            requestId: $requestId,
            content: $content,
            bodySiteCode: $bodySiteCode,
            operator: $operator,
            subject: $subject
        );
    }
}
