<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\JoinRoomRequest;
use App\Parents\Controller;
use App\Parents\Request;
use App\Services\CreateRoomService;
use App\Services\RoomQueueService;
use App\Services\JoinRoomService;
use Illuminate\Http\JsonResponse;

final class RoomController extends Controller
{
    public function create(Request $request): JsonResponse
    {
        $roomId = (new CreateRoomService())->run(
            (int)$request->user()->getAuthIdentifier()
        );

        return $this->json(['roomId' => $roomId]);
    }

    public function join(JoinRoomRequest $request): JsonResponse
    {
        (new JoinRoomService())->run(
            $request->route('roomId'),
            (int)$request->user()->getAuthIdentifier()
        );

        return $this->json(status: 204);
    }

    public function startSearch(Request $request): JsonResponse
    {
        (new RoomQueueService())->enqueue(
            $request->user()->getAuthIdentifier()
        );

        return $this->json(status: 204);
    }

    public function stopSearch(Request $request): JsonResponse
    {
        (new RoomQueueService())->dequeue(
            $request->user()->getAuthIdentifier()
        );

        return $this->json(status: 204);
    }
}