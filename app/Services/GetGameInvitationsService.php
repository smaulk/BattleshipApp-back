<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\GetGameInvitationsDto;
use App\Dto\PaginateDto;
use App\Models\GameInvitation;
use App\Services\Abstract\PaginateService;
use Illuminate\Support\Collection;

final class GetGameInvitationsService extends PaginateService
{
    public function run(GetGameInvitationsDto $dto): PaginateDto
    {
        return $this->paginate($this->fetchInvites($dto));
    }

    private function fetchInvites(GetGameInvitationsDto $dto): Collection
    {
        return GameInvitation::query()
            ->when(is_null($dto->type), function ($query) use ($dto) {
                $query->where('sender_id', $dto->userId)
                    ->orWhere('receiver_id', $dto->userId);
            })
            ->when($dto->type === 1, function ($query) use ($dto) {
                $query->where('sender_id', $dto->userId)
                    ->with(['receiver' => function ($query) {
                        $query->select('id', 'nickname', 'avatar_filename');
                    }]);
            })
            ->when($dto->type === 2, function ($query) use ($dto) {
                $query->where('receiver_id', $dto->userId)
                    ->with(['sender' => function ($query) {
                        $query->select('id', 'nickname', 'avatar_filename');
                    }]);
            })
            ->when(!empty($dto->startId), function ($query) use ($dto) {
                $query->whereRaw('UNIX_TIMESTAMP(invited_at) < ?', [$dto->startId]);
            })
            ->orderByDesc($this->getPaginateId())
            ->limit($this->getLimit())
            ->get();
    }

    protected function getPaginateId(): string
    {
        return 'invited_at';
    }
}