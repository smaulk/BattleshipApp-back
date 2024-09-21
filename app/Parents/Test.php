<?php

namespace App\Parents;

use App\Classes\Auth\Jwt;
use App\Parents\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Notification;

abstract class Test extends BaseTestCase
{
    use LazilyRefreshDatabase;

    protected Jwt $jwt;

    public function __construct(string $name)
    {
        $this->jwt = new Jwt();
        parent::__construct($name);
    }

    /**
     * Предотвращает выполнение слушателей событий за исключением событий модели
     */
    protected function fakeEventWithModel(): void
    {
        Notification::fake();
        $dispatcher = Event::getFacadeRoot();
        Event::fake();
        Model::setEventDispatcher($dispatcher);
    }
}
