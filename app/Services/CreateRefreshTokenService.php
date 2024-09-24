<?php
declare(strict_types=1);

namespace App\Services;

use App\Classes\Timestamp;
use App\Dto\CreateRefreshTokenDto;
use App\Models\RefreshToken;
use App\Parents\Service;
use Illuminate\Support\Str;
use Throwable;

final class CreateRefreshTokenService extends Service
{
    /**
     * Создает новый Refresh Token
     * @throws Throwable
     */
    public function run(CreateRefreshTokenDto $dto): RefreshToken
    {
        $refresh = new RefreshToken();
        $refresh->ulid = Str::ulid()->toBase32();
        $refresh->chain = $dto->chain ?? Str::ulid()->toBase32();
        $refresh->user_id = $dto->userId;
        $refresh->ip_address = $dto->ipAddress;
        $refresh->user_agent = $dto->userAgent;
        $refresh->expires_at = Timestamp::now()->addDays($this->getRefreshTtl())->get();
        $refresh->saveOrFail();

        return $refresh;
    }

    private function getRefreshTtl(): int
    {
        return (int)config('auth.jwt.refresh.ttl');
    }
}
