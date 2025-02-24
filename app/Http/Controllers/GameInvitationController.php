<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Dto\FriendshipDto;
use App\Http\Requests\AuthorizedRequest;
use App\Http\Requests\CreateFriendshipRequest;
use App\Models\GameInvitation;
use App\Parents\Controller;
use App\Parents\Request;
use App\Services\AcceptGameInvitationService;
use App\Services\CreateGameInvitationService;
use App\Services\DeleteGameInvitationService;
use Illuminate\Http\JsonResponse;

final class GameInvitationController extends Controller
{
    public function getOutgoing(AuthorizedRequest $request): JsonResponse
    {
        $invitations = GameInvitation::query()
            ->where('sender_id', $request->getUserId())
            ->orderByDesc('invited_at')
            ->get();

        return $this->json($invitations);
    }

    public function getIncoming(AuthorizedRequest $request): JsonResponse
    {
        $invitations = GameInvitation::query()
            ->where('receiver_id', $request->getUserId())
            ->orderByDesc('invited_at')
            ->get();

        return $this->json($invitations);
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
}
