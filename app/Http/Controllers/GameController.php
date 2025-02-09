<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CreateGameRequest;
use App\Http\Requests\GetUserGamesRequest;
use App\Http\Resources\UserGameResource;
use App\Models\Game;
use App\Models\User;
use App\Parents\Controller;
use App\Parents\Request;
use App\Services\CreateGameService;
use App\Services\GetUserGamesService;
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

    public function create(CreateGameRequest $request): JsonResponse
    {
        $game = (new CreateGameService())->run(
            $request->toDto()
        );

        return $this->json($game, 201);
    }
}
