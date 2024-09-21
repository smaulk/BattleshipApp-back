<?php

namespace App\Providers;

use App\Classes\AvatarManager;
use App\Exceptions\Handler;
use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Contracts\Debug\ExceptionHandler;
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
        //Переопределение Handler для обработки ошибок
        $this->app->singleton(ExceptionHandler::class, Handler::class);

        $this->registerObservers();
    }

    private function registerObservers(): void
    {
        User::observe(UserObserver::class);
    }
}
