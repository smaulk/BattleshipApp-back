<?php
declare(strict_types=1);

namespace App\Parents;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class Controller
{
    /**
     * Создает новый экземпляр JSON ответа
     */
    protected function json(
        mixed $data = [],
        int   $status = 200,
        array $headers = [],
        int   $options = 0
    ): JsonResponse
    {
        return response()->json($data, $status, $headers, $options);
    }

    /**
     * Возвращает JsonResource от объекта
     *
     * @param array|object $item
     * @param class-string<JsonResource> $resource
     * @return JsonResource
     */
    protected function resource(array|object $item, string $resource): JsonResource
    {
        return new $resource($item);
    }

    /**
     * Вовзращает коллекцию ресурсов из объектов
     *
     * @param iterable $items
     * @param class-string<JsonResource> $resource
     * @return AnonymousResourceCollection
     */
    protected function collection(iterable $items, string $resource): AnonymousResourceCollection
    {
        return $resource::collection($items);
    }
}
