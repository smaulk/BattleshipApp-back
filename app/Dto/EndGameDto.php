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

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->gameId = (int)$data['gameId'];
        $dto->userId = (int)$data['userId'];
        $dto->type = GameType::from((int)$data['type']);

        return $dto;
    }

    public static function fromRequest(FinishGameRequest $request): self
    {
        return self::fromArray([
            'gameId' => $request->route('gameId'),
            'userId' => $request->user()->getAuthIdentifier(),
            'type'   => $request->validated('type')
        ]);
    }
}