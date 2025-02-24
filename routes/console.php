<?php

use App\Services\AbandonGamesService;
use App\Services\ClearRefreshService;
use Illuminate\Support\Facades\Schedule;

Schedule::call(new ClearRefreshService())->daily();
Schedule::call(new AbandonGamesService())->everyTenMinutes();