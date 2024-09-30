<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PaginateCollection extends ResourceCollection
{
    protected ?int $lastId;

    public function __construct(ResourceCollection $resource, ?int $lastId)
    {
        $this->lastId = $lastId;
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'lastId' => $this->lastId,
            ],
        ];
    }
}
