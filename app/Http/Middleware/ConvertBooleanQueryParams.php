<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ConvertBooleanQueryParams
{
    /**
     * Преобразуем булевы значения из параметра GET запроса
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('get')) {
            // Проходим по всем параметрам запроса
            foreach ($request->query() as $key => $value) {
                // Если параметр имеет строку "true" или "false", преобразуем в булевое значение
                if (is_string($value) && in_array(strtolower($value), ['true', 'false'], true)) {
                    $request->merge([
                        $key => filter_var($value, FILTER_VALIDATE_BOOLEAN)
                    ]);
                }
            }
        }

        return $next($request);
    }
}
