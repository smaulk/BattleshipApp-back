<?php
declare(strict_types=1);

namespace App\Dto;

use App\Enums\ShipPosition;
use App\Enums\ShotStatus;
use App\Http\Requests\SendMoveResultRequest;
use App\Parents\Dto;

final readonly class SendMoveResultDto extends Dto
{
    public int $userId;
    public int $gameId;
    public ShotDataDto $shotData;

    public static function fromRequest(SendMoveResultRequest $request): self
    {
        $dto = new self();
        $dto->userId = (int)$request->user()?->getAuthIdentifier();
        $dto->gameId = (int)$request->route('gameId');

        $shipData = $request->validated('ship');
        $shipDataDto = $shipData
            ? new ShipDataDto(
                (int)$shipData['id'],
                (int)$shipData['size'],
                ShipPosition::from((int)$shipData['position'])
            )
            : null;

        $startCell = $request->validated('startCell');
        $startCellDto = $startCell
            ? new ColRowDataDto(
                (int)$startCell['col'],
                (int)$startCell['row'],
            )
            : null;

        $dto->shotData = new ShotDataDto(
            ShotStatus::from((int)$request->validated('status')),
            $shipDataDto,
            $startCellDto,
        );

        return $dto;
    }
}