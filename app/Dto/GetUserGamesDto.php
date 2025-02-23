<?php
declare(strict_types=1);

namespace App\Dto;

use App\Enums\GameType;
use App\Http\Requests\GetUserGamesRequest;
use App\Parents\Dto;

final readonly class GetUserGamesDto extends Dto
{
    public int $userId;
    public ?int $startId;
    public ?GameType $type;

    public static function fromRequest(GetUserGamesRequest $request): self
    {
        $type = $request->validated('type');

        $dto = new self();
        $dto->userId = $request->getUserId();
        $dto->startId = (int)$request->validated('startId');
        $dto->type = $type ? GameType::from((int)$type): null;

        return $dto;
    }
}