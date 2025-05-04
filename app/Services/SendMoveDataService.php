<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\SendMoveDataDto;
use App\Events\SendMoveData;
use App\Services\Abstract\GameService;

class SendMoveDataService extends GameService
{
    public function run(SendMoveDataDto $dto): void
    {
        $this->validate($dto->userId, $this->getGame($dto->gameId));
        SendMoveData::broadcast($dto->userId, $dto->gameId, $dto->cell)->toOthers();
    }
}