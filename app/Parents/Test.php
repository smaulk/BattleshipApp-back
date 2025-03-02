<?php
declare(strict_types=1);

namespace App\Parents;

use App\Classes\Auth\Jwt;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Redis;

abstract class Test extends BaseTestCase
{
    use LazilyRefreshDatabase;

    protected Jwt $jwt;

    public function __construct(string $name)
    {
        $this->jwt = new Jwt();
        parent::__construct($name);
    }

    protected function setUp(): void
    {
        parent::setUp();
        // Очищаем базу данных Redis
        Redis::flushdb();
    }

    /**
     * Предотвращает выполнение слушателей событий за исключением событий модели
     */
    protected function fakeEventWithModel(): void
    {
        $dispatcher = Event::getFacadeRoot();
        Event::fake();
        Model::setEventDispatcher($dispatcher);
        Notification::fake();
    }
}
