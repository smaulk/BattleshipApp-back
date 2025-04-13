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

        if ($invitation->relationLoaded('sender')) {
            $user = $invitation->sender;
        } elseif ($invitation->relationLoaded('receiver')) {
            $user = $invitation->receiver;
        } else {
            return [];
        }

        return [
            'friendId'  => $user->id,
            'nickname'  => $user->nickname,
            'avatarUrl' => $user->avatar_url,
            'invitedAt' => $invitation->invited_at,
        ];
    }
}