<?php

namespace Legit\Dapi\MediaAnalyzerArguments;

use Legit\Dapi\MediaAnalyzerArguments\BodySite\BodySiteCode;
use Legit\Dapi\MediaAnalyzerArguments\Operator\Operator;
use Legit\Dapi\MediaAnalyzerArguments\Subject\Subject;

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
