<?php

namespace Legit\Dapi\MediaAnalyzerArguments\BodySite;

enum BodySiteCode: string
{
    case HeadFront = 'HEAD_FRONT';
    case HeadBack = 'HEAD_BACK';
    case ArmLeft = 'ARM_LEFT';
    case ArmRight = 'ARM_RIGHT';
    case TrunkFront = 'TRUNK_FRONT';
    case TrunkBack = 'TRUNK_BACK';
    case LegLeft = 'LEG_LEFT';
    case LegRight = 'LEG_RIGHT';
    case HandLeft = 'HAND_LEFT';
    case HandRight = 'HAND_RIGHT';
    case FootLeft = 'FOOT_LEFT';
    case FootRight = 'FOOT_RIGHT';
    case Genital = 'GENITAL';
    case Scalp = 'SCALP';
    case EarLeft = 'EAR_LEFT';
    case EarRight = 'EAR_RIGHT';
    case Perioral = 'PERIORAL';
    case Tongue = 'TONGUE';
    case ElbowLeft = 'ELBOW_LEFT';
    case ElbowRight = 'ELBOW_RIGHT';
    case UpperArmLeft = 'UPPER_ARM_LEFT';
    case UpperArmRight = 'UPPER_ARM_RIGHT';
    case LowerArmLeft = 'LOWER_ARM_LEFT';
    case LowerArmRight = 'LOWER_ARM_RIGHT';
    case KneeLeft = 'KNEE_LEFT';
    case KneeRight = 'KNEE_RIGHT';
    case UpperLegLeft = 'UPPER_LEG_LEFT';
    case UpperLegRight = 'UPPER_LEG_RIGHT';
    case LowerLegLeft = 'LOWER_LEG_LEFT';
    case LowerLegRight = 'LOWER_LEG_RIGHT';
}
