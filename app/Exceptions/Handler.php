<?php
declare(strict_types=1);

namespace App\Exceptions;

use App\Parents\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class Handler extends ExceptionHandler
{
    public function render($request, Throwable $e): Response
    {
        if ($e instanceof ModelNotFoundException) {
            $model = $e->getModel();
            $message = is_subclass_of($model, Model::class)
                ? $model::getNotFoundMessage()
                : 'Данные не найдены';
            return $this->json(404, $message);
        }

        if($e instanceof HttpResponseException) {
            return parent::render($request, $e);
        }

        return $this->json(500, 'Ошибка сервера');
    }

    private function json(int $status, string $message): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], $status);
    }
}