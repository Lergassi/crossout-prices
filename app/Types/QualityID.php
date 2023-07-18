<?php

namespace App\Types;

enum QualityID: string
{
    case Common = 'common';
    case Rare = 'rare';
    case Special = 'special';
    case Epic = 'epic';
    case Legendary = 'legendary';
    case Relic = 'relic';
}