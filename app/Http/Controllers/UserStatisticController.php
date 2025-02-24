<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\UserStatisticResource;
use App\Models\UserStatistic;
use App\Parents\Controller;
use App\Parents\Request;
use App\Services\GetLeaderBoardService;
use Illuminate\Http\JsonResponse;

final class UserStatisticController extends Controller
{
    public function get(Request $request): JsonResponse
    {
        $statistic = UserStatistic::findOrFail(
            (int)$request->route('userId')
        );

        return $this
            ->resource($statistic, UserStatisticResource::class)
            ->response();
    }

    public function getLeaderBoard(Request $request): JsonResponse
    {
        $leaders = (new GetLeaderBoardService())->run();

        return $this
            ->collection($leaders, UserStatisticResource::class)
            ->response();
    }
}