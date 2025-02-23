<?php
declare(strict_types=1);

namespace App\Parents;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

abstract class BroadcastEvent extends Event implements ShouldBroadcast
{
    public string $queue = 'broadcasts';
}