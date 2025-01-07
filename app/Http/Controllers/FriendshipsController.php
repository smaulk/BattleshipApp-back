<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Dto\FriendshipDto;
use App\Enums\FriendshipType;
use App\Http\Requests\CreateFriendshipRequest;
use App\Http\Requests\GetUsersByFriendshipRequest;
use App\Http\Resources\UserResource;
use App\Parents\Controller;
use App\Parents\Request;
use App\Services\AcceptFriendshipService;
use App\Services\CreateFriendshipService;
use App\Services\DeleteFriendshipService;
use App\Services\GetUsersByFriendshipService;
use Illuminate\Http\JsonResponse;

final class FriendshipsController extends Controller
{
    public function getFriends(GetUsersByFriendshipRequest $request): JsonResponse
    {
        return $this->getFriendships($request, FriendshipType::FRIEND);
    }

    public function getOutgoing(GetUsersByFriendshipRequest $request): JsonResponse
    {
        return $this->getFriendships($request, FriendshipType::OUTGOING);
    }

    public function getIncoming(GetUsersByFriendshipRequest $request): JsonResponse
    {
        return $this->getFriendships($request, FriendshipType::INCOMING);
    }

    private function getFriendships(GetUsersByFriendshipRequest $request, FriendshipType $type): JsonResponse
    {
        $dto = (new GetUsersByFriendshipService())->run(
            $request->toDto($type)
        );

        return $this
            ->paginate($dto, UserResource::class)
            ->response();
    }

    public function create(CreateFriendshipRequest $request): JsonResponse
    {
        (new CreateFriendshipService())->run(
            $request->toDto(),
        );

        return $this->json(status: 201);
    }

    public function accept(Request $request): JsonResponse
    {
        (new AcceptFriendshipService())->run(
            FriendshipDto::fromRequest($request),
        );

        return $this->json(status: 204);
    }

    public function delete(Request $request): JsonResponse
    {
        (new DeleteFriendshipService())->run(
            FriendshipDto::fromRequest($request)
        );

        return $this->json(status: 204);
    }
}
