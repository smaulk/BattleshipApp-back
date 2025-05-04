<?php
declare(strict_types=1);

namespace App\Enums;

enum ShotStatus: int
{
    case MISS = 1;
    case HIT = 2;
    case DESTROYED = 3;
}