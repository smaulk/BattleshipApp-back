<?php
declare(strict_types=1);

namespace App\Dto;

use App\Enums\GameStatus;
use App\Parents\Dto;

final readonly class CreateGameDto extends Dto
{
    public int $uid1;
    public int $uid2;
    public ?GameStatus $status;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->uid1 = (int)$data['uid1'];
        $dto->uid2 = (int)$data['uid2'];
        $dto->status = !empty($data['status']) ? GameStatus::from((int)$data['status']) : null;

        return $dto;
    }
}