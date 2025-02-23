<?php
declare(strict_types=1);

namespace App\Classes\WebSocket\Handlers;

use App\Classes\WebSocket\Requests\WebHookRequest;

abstract class Handler
{
    public abstract function __invoke(WebHookRequest $request): void;
}