<?php
declare(strict_types=1);

namespace App\Http\Controllers;

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

        return $this->json($statistic);
    }

    public function getLeaderBoard(Request $request): JsonResponse
    {
        $leaders = (new GetLeaderBoardService())->run();

        return $this->json($leaders);
    }
}