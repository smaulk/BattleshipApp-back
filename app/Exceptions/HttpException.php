<?php
declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Http\Exceptions\HttpResponseException;

class HttpException extends HttpResponseException
{
    public function __construct(int $status, string $message = 'Ошибка сервера')
    {
        parent::__construct(response()->json([
            'message' => $message
        ], $status));
    }
}
