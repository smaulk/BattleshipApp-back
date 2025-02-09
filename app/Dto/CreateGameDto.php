<?php
declare(strict_types=1);

namespace App\Dto;

use App\Enums\GameStatus;
use App\Http\Requests\CreateGameRequest;
use App\Parents\Dto;

final readonly class CreateGameDto extends Dto
{
    public int $uid1;
    public int $uid2;
    public GameStatus $status;

    public static function fromRequest(CreateGameRequest $request): self
    {
        $status = $request->validated('status') ?? GameStatus::CREATED->value;

        $dto = new self();
        $dto->uid1 = (int)$request->validated('uid1');
        $dto->uid2 = (int)$request->validated('uid2');
        $dto->status = GameStatus::from((int)$status);

        return $dto;
    }
}