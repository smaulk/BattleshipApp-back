<?php
declare(strict_types=1);

namespace App\Dto;

use App\Enums\ShipPosition;
use App\Parents\Dto;

final readonly class ShipDataDto extends Dto
{
    public function __construct(
        public int $id,
        public int $size,
        public ShipPosition $position,
    )
    {}
}