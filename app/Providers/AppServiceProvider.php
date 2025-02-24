<?php

namespace App\Providers;

use App\Exceptions\ExceptionHandler;
use App\Models\Game;
use App\Models\User;
use App\Observers\GameObserver;
use App\Observers\UserObserver;
use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //Переопределение ExceptionHandler для обработки ошибок
        $this->app->singleton(ExceptionHandlerContract::class, ExceptionHandler::class);

        $this->registerObservers();
    }

    private function registerObservers(): void
    {
        User::observe(UserObserver::class);
        Game::observe(GameObserver::class);
    }
}
