<?php
declare(strict_types=1);

namespace App\Enums;

enum ShipPosition: int
{
    case HORIZONTAL = 1;
    case VERTICAL = 2;
}