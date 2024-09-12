<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class Controller
{
    /**
     * Create a new JSON response instance.
     */
    protected function json(
        mixed $data = [],
        int $status = 200,
        array $headers = [],
        int $options = 0
    ): JsonResponse {
        return response()->json($data, $status, $headers, $options);
    }

    /**
     * Returns JsonResource of item
     *
     * @param array|object $item
     * @param class-string<JsonResource> $resource
     * @return JsonResource
     */
    protected function resource(array|object $item, string $resource): JsonResource
    {
        return new $resource($item);
    }
}
