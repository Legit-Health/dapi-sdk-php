<?php

namespace LegitHealth\Dapi\MediaAnalyzerArguments;

use LegitHealth\Dapi\MediaAnalyzerArguments\BodySite\BodySiteCode;
use LegitHealth\Dapi\MediaAnalyzerArguments\Operator\Operator;
use LegitHealth\Dapi\MediaAnalyzerArguments\Subject\Subject;
use LegitHealth\Dapi\MediaAnalyzerArguments\View\View;

final readonly class DiagnosisSupportData extends MediaAnalyzerData
{
    /**
     * @param string[] $content
     * @param BodySiteCode|null $bodySiteCode
     * @param Operator|null $operator
     * @param Subject|null $subject
     * @param View|null $view
     */
    public function __construct(
        array $content,
        ?BodySiteCode $bodySiteCode = null,
        ?Operator $operator = null,
        ?Subject $subject = null,
        ?View $view = null
    ) {
        parent::__construct(
            content: $content,
            bodySiteCode: $bodySiteCode,
            operator: $operator,
            subject: $subject,
            view: $view
        );
    }
}
