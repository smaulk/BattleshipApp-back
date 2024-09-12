<?php
declare(strict_types=1);

namespace App\Services;

use App\Dto\CreateRefreshTokenDto;
use App\Dto\RefreshDto;
use App\Exceptions\HttpException;
use App\Models\RefreshToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class RefreshService
{

    public function run(RefreshDto $dto): array
    {
        $refreshToken = $this->getRefreshTokenModel($dto->ulid);
        $this->validate($refreshToken);
        $refreshToken->block();

        try {
            $refreshToken = (new CreateRefreshTokenService())
                ->run(new CreateRefreshTokenDto(
                    $refreshToken->chain, $refreshToken->user_id, $dto->ipAddress, $dto->userAgent
                ));
            $accessToken = Auth::loginUsingId($refreshToken->user_id);
        } catch (Throwable $exception) {
            Log::error($exception);
            throw new HttpException(500);
        }

        return [$accessToken, $refreshToken->ulid];
    }

    /**
     * Возвращает модель Refresh Token по ulid
     */
    private function getRefreshTokenModel(string $ulid): ?RefreshToken
    {
        /** @var RefreshToken|null */
        return RefreshToken::query()->find($ulid);
    }

    private function validate(?RefreshToken $refreshToken): void
    {
        if (is_null($refreshToken)) {
            throw new HttpException(401, 'Сессия не найдена');
        }
        if ($refreshToken->is_blocked) {
            $this->blockTokenChain($refreshToken->chain);
            throw new HttpException(401, 'Сессия была заблокирована');
        }
        if ($refreshToken->expired_in < now()) {
            $refreshToken->block();
            throw new HttpException(401, 'Срок действия токена истек');
        }
    }

    private function blockTokenChain(string $chain): void
    {
        RefreshToken::query()
            ->where('chain', $chain)
            ->update([
                'is_blocked' => 1,
            ]);
    }
}
