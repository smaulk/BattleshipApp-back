<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Dto\FriendshipDto;
use App\Http\Requests\AuthorizedRequest;
use App\Http\Requests\CreateFriendshipRequest;
use App\Http\Requests\GetGameInvitationsRequest;
use App\Http\Resources\GameInvitationResource;
use App\Parents\Controller;
use App\Parents\Request;
use App\Services\AcceptGameInvitationService;
use App\Services\CreateGameInvitationService;
use App\Services\DeleteAlIGameInvitationsService;
use App\Services\DeleteGameInvitationService;
use App\Services\GetGameInvitationsService;
use Illuminate\Http\JsonResponse;

final class GameInvitationController extends Controller
{
    public function getOutgoing(GetGameInvitationsRequest $request): JsonResponse
    {
        $dto = (new GetGameInvitationsService())->run(
            $request->toDto(1)
        );

        return $this
            ->paginate($dto, GameInvitationResource::class)
            ->response();
    }

    public function getIncoming(GetGameInvitationsRequest $request): JsonResponse
    {
        $dto = (new GetGameInvitationsService())->run(
            $request->toDto(2)
        );

        return $this
            ->paginate($dto, GameInvitationResource::class)
            ->response();
    }

    public function create(CreateFriendshipRequest $request): JsonResponse
    {
        (new CreateGameInvitationService())->run(
            $request->toDto()
        );

        return $this->json(status: 201);
    }

    public function accept(Request $request): JsonResponse
    {
        (new AcceptGameInvitationService())->run(
            FriendshipDto::fromRequest($request)
        );

        return $this->json(status: 204);
    }

    public function delete(Request $request): JsonResponse
    {
        (new DeleteGameInvitationService())->run(
            FriendshipDto::fromRequest($request)
        );

        return $this->json(status: 204);
    }

    public function deleteAll(AuthorizedRequest $request): JsonResponse
    {
        (new DeleteAlIGameInvitationsService())->run(
            [$request->getUserId()]
        );

        return $this->json(status: 204);
    }
}
