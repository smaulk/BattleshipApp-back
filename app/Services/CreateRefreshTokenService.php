<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\CreateRefreshTokenDto;
use App\Models\RefreshToken;
use Illuminate\Support\Str;
use Throwable;

class CreateRefreshTokenService
{
    /**
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
        $refresh->expired_in = time() + env('REFRESH_TOKEN_LIFETIME', 2592000);
        $refresh->saveOrFail();

        return $refresh;
    }
}
