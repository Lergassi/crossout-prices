<?php

namespace App\Types;

enum CategoryID: string
{
    case Resources = 'Resources';
    case Cabins = 'Cabins';
    case Weapons = 'Weapons';
    case Hardware = 'Hardware';
    case Movement = 'Movement';
}