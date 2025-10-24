<?php

namespace App\Enum;

enum Etat: int
{
    case Miteux = 1;
    case Endommagé = 2;
    case Passable = 3;
    case Bon = 4;
    case TrèsBon = 5;
    case Neuf = 6;
}