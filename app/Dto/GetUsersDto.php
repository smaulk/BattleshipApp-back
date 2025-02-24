<?php
declare(strict_types=1);

namespace App\Dto;

use App\Http\Requests\GetUsersRequest;
use App\Parents\Dto;

final readonly class GetUsersDto extends Dto
{
    public int $userId;
    public string $nickname;
    public ?int $startId;
    public ?bool $is_online;

    public static function fromRequest(GetUsersRequest $request): GetUsersDto
    {
        $startId = $request->validated('startId');
        $is_online = $request->validated('is_online');

        $dto = new self();
        $dto->userId = $request->user()?->getAuthIdentifier();
        $dto->nickname = $request->validated('nickname');
        $dto->startId = $startId ? (int)$startId : null;
        $dto->is_online = !is_null($is_online) ? (bool)$is_online : null;

        return $dto;
    }
}
