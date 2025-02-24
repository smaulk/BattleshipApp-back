<?php
declare(strict_types=1);

namespace App\Dto;

use App\Http\Requests\GetGameInvitationsRequest;
use App\Parents\Dto;

final readonly class GetGameInvitationsDto extends Dto
{
    public int $userId;
    public ?int $startId;
    public ?int $type;

    public static function fromRequest(GetGameInvitationsRequest $request, ?int $type = null): GetGameInvitationsDto
    {
        $startId = $request->validated('startId');

        $dto = new self();
        $dto->userId = $request->getUserId();
        $dto->startId = $startId ? (int)$startId : null;
        $dto->type = $type;
        return $dto;
    }
}