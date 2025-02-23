<?php
declare(strict_types=1);

namespace App\Parents;

use Illuminate\Foundation\Events\Dispatchable;

abstract class Event
{
    use Dispatchable;
}