<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MeToIdMiddleware
{
    /**
     * Заменяет "me" в роуте на id авторизованного пользователя
     *
     * @throws AuthorizationException
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Route $route */
        $route = $request->route();
        // Если в роуте нет параметра {userId}, или он не равен "me", пропускаем запрос дальше
        if (!$route->hasParameter('userId') || $route->parameter('userId') !== 'me') {
            return $next($request);
        }
        // Получаем id авторизованного пользователя
        $userId = Auth::id();
        if (!$userId) {
            throw new AuthorizationException;
        }
        $route->setParameter('userId', $userId);
        return $next($request);
    }
}
