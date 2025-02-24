<?php
declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\GameInvitation;
use App\Parents\JsonResource;
use Illuminate\Http\Request;

class GameInvitationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var GameInvitation $invitation */
        $invitation = $this->resource;
        $currentUserId = $request->user()?->getKey();

        return [
            'friendId'  => $currentUserId == $invitation->sender_id
                ? $invitation->receiver_id
                : $invitation->sender_id,
            'invitedAt' => $invitation->invited_at,
        ];
    }
}