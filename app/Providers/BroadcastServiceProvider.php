<?php

namespace App\Providers;

use App\Classes\WebSocket\Handlers\GameHandler;
use App\Classes\WebSocket\Handlers\OnlineHandler;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Broadcast::routes(['middleware' => ['api']]);
        $this->loadWebhookHandlers();

        require base_path('routes/channels.php');
    }

    private function loadWebhookHandlers(): void
    {
        Route::post('/broadcasting/webhook-online', OnlineHandler::class)->middleware('api');
        Route::post('/broadcasting/webhook-game', GameHandler::class)->middleware('api');
    }
}
