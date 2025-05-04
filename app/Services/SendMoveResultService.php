<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\SendMoveResultDto;
use App\Events\SendMoveResult;
use App\Exceptions\HttpException;
use App\Services\Abstract\GameService;

class SendMoveResultService extends GameService
{
    public function run(SendMoveResultDto $dto): void
    {
        $this->validate($dto->userId, $this->getGame($dto->gameId));
        SendMoveResult::broadcast($dto->userId, $dto->gameId, $dto->shotData)->toOthers();
    }
}