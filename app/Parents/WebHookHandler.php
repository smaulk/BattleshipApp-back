<?php
declare(strict_types=1);

namespace App\Parents;

use App\Classes\WebSocket\Requests\WebHookRequest;

abstract class WebHookHandler
{
    public abstract function __invoke(WebHookRequest $request): void;
}