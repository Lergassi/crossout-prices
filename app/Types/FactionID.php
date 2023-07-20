<?php

namespace App\Types;

enum FactionID: string
{
    case Engineers = 'Engineers';
    case Lunatics = 'Lunatics';
    case Nomads = 'Nomads';
    case Scavengers = 'Scavengers';
    case SteppenWolfs = 'Steppenwolfs';
    case DawnChildren = 'Dawn\'s Children';
    case FireStarters = 'Firestarters';
}