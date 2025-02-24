<?php

namespace App\Providers;

use App\Classes\WebSocket\Handlers\GameWebHookHandler;
use App\Classes\WebSocket\Handlers\OnlineWebHookHandler;
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
        Route::post('/broadcasting/webhook-online', OnlineWebHookHandler::class)->middleware('api');
        Route::post('/broadcasting/webhook-game', GameWebHookHandler::class)->middleware('api');
    }
}
