<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\RefreshToken;
use App\Parents\Service;

/**
 * Удаляет истекшие refresh токены (истекшие второй раз)
 */
final class ClearRefreshService extends Service
{
    public function __invoke(): void
    {
        RefreshToken::query()
            ->where('expires_at', '<',  now()->subDays($this->getRefreshTtl()))
            ->delete();
    }

    private function getRefreshTtl(): int
    {
        return (int)config('auth.jwt.refresh.ttl');
    }
}