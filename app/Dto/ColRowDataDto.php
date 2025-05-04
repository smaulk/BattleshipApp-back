<?php
declare(strict_types=1);

namespace App\Dto;

use App\Parents\Dto;

final readonly class ColRowDataDto extends Dto
{
    public function __construct(
        public int $col,
        public int $row,
    )
    {}
}