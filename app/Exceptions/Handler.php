<?php
declare(strict_types=1);

namespace App\Exceptions;

use App\Parents\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Throwable;

final class Handler extends ExceptionHandler
{
    public function render($request, Throwable $e): Response
    {
        if ($e instanceof ModelNotFoundException) {
            $model = $e->getModel();
            $message = is_subclass_of($model, Model::class)
                ? $model::getNotFoundMessage()
                : Model::getNotFoundMessage();
            return $this->json(404, $message);
        }

        if($e instanceof HttpResponseException) {
            return parent::render($request, $e);
        }
        if($e instanceof AccessDeniedHttpException){
            return $this->json(403, 'Доступ запрещен');
        }
        if($e instanceof ValidationException){
            return response()->json([
                'message' => "Ошибка валидации данных",
                'errors' => $e->errors()
            ], 422);
        }

        //return parent::render($request, $e);
        return $this->json(500, 'Ошибка сервера');
    }

    private function json(int $status, string $message): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], $status);
    }
}