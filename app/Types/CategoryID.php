<?php

namespace App\Types;

enum CategoryID: string
{
    case Resource = 'resource';
    case Cabins = 'cabins';
    case Weapons = 'weapons';
    case Hardware = 'hardware';
    case Movement = 'movement';
}