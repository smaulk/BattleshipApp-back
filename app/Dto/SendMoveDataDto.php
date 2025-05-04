<?php
declare(strict_types=1);

namespace App\Dto;

use App\Http\Requests\SendMoveDataRequest;
use App\Parents\Dto;

final readonly class SendMoveDataDto extends Dto
{
    public int $userId;
    public int $gameId;
    public ColRowDataDto $cell;

    public static function fromRequest(SendMoveDataRequest $request): self
    {
        $dto = new self();
        $dto->userId = (int)$request->user()?->getAuthIdentifier();
        $dto->gameId = (int)$request->route('gameId');
        $dto->cell = new ColRowDataDto(
            (int)$request->validated('col'),
            (int)$request->validated('row')
        );

        return $dto;
    }
}