<?php

use App\Services\AbandonGamesService;
use Illuminate\Support\Facades\Schedule;

Schedule::call(new AbandonGamesService())->everyTenMinutes();