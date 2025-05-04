<?php
declare(strict_types=1);

namespace App\Dto;

use App\Enums\ShotStatus;
use App\Parents\Dto;

final readonly class ShotDataDto extends Dto
{
    public function __construct(
        public ShotStatus $status,
        public ?ShipDataDto $ship,
        public ?ColRowDataDto $startCell,
    )
    {}
}