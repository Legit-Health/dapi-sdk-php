<?php

namespace Legit\Dapi\MediaAnalyzerArguments\Subject;

enum Gender: string
{
    case Male = 'male';
    case Female = 'female';
    case Other = 'other';
    case unknown = 'unknown';
}
