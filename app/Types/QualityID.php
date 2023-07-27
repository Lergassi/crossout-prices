<?php

namespace App\Types;

enum QualityID: string
{
    case Common = 'Common';
    case Rare = 'Rare';
    case Special = 'Special';
    case Epic = 'Epic';
    case Legendary = 'Legendary';
    case Relic = 'Relic';
}