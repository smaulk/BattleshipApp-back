<?php
declare(strict_types=1);

namespace App\Dto;

use App\Parents\Dto;

final readonly class PaginateDto extends Dto
{
    public function __construct(
        public iterable $items,
        public ?int $lastId,
    ){}
}