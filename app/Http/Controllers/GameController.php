<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\FinishGameRequest;
use App\Http\Requests\GetUserGamesRequest;
use App\Http\Requests\SendMoveDataRequest;
use App\Http\Requests\SendMoveResultRequest;
use App\Http\Resources\UserGameResource;
use App\Parents\Controller;
use App\Parents\Request;
use App\Services\FinishGameService;
use App\Services\GetUserGamesService;
use App\Services\SendMoveDataService;
use App\Services\SendMoveResultService;
use App\Services\StartGameService;
use Illuminate\Http\JsonResponse;

final class GameController extends Controller
{
    public function get(GetUserGamesRequest $request): JsonResponse
    {
        $dto = (new GetUserGamesService())->run(
            $request->toDto()
        );

        return $this
            ->paginate($dto, UserGameResource::class)
            ->response();
    }

    public function start(Request $request): JsonResponse
    {
        (new StartGameService())->run(
            (int)$request->user()->getAuthIdentifier(),
            $request->route('roomId')
        );

        return $this->json(status: 204);
    }

    public function finish(FinishGameRequest $request): JsonResponse
    {
        (new FinishGameService())->run(
            $request->toDto()
        );

        return $this->json(status: 204);
    }

    public function sendMoveData(SendMoveDataRequest $request): JsonResponse
    {
        (new SendMoveDataService())->run(
            $request->toDto()
        );

        return $this->json(status: 204);
    }

    public function sendMoveResult(SendMoveResultRequest $request): JsonResponse
    {
        (new SendMoveResultService())->run(
            $request->toDto()
        );

        return $this->json(status: 204);
    }
}
