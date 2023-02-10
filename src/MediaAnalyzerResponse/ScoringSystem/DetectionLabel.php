<?php

namespace LegitHealth\Dapi\MediaAnalyzerResponse\ScoringSystem;

enum DetectionLabel: string
{
    case AcneLesion = 'Acne lesion';
    case Hive = 'Hive';
    case DrainingTunnel = 'Draining tunnel';
    case Nodule = 'Nodule';
    case Abscess = 'Abscess';
}
