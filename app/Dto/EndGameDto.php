<?php
declare(strict_types=1);

namespace App\Dto;

use App\Enums\GameType;
use App\Http\Requests\FinishGameRequest;
use App\Parents\Dto;

final readonly class EndGameDto extends Dto
{
    public int $gameId;
    public int $userId;
    public GameType $type;

    public static function fromRequest(FinishGameRequest $request): self
    {
        $dto = new self();
        $dto->gameId = (int)$request->route('gameId');
        $dto->userId = (int)$request->user()->getAuthIdentifier();
        $dto->type = GameType::from((int)$request->validated('type'));

        return $dto;
    }
}