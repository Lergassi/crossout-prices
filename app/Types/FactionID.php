<?php

namespace App\Types;

enum FactionID: string
{
    case Engineers = 'engineers';
    case Lunatics = 'lunatics';
    case Nomads = 'nomads';
    case Scavengers = 'scavengers';
    case SteppenWolfs = 'steppen_wolfs';
    case DawnChildren = 'dawn_children';
    case FireStarters = 'fire_starters';
}