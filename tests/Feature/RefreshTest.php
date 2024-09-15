<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class RefreshTest extends TestCase
{
    public function testRefresh(): void
    {
        // Создаем пользователя
        $user = User::factory()->create([
            'nickname' => Str::random(),
            'email'    => $email = Str::random() . '@example.com',
            'password' => $password = Str::password(),
        ]);

        // Получаем рефреш токен
        $refreshToken = $this->getRefreshToken($email, $password);

        $this
            ->assertDatabaseHas(RefreshToken::class, [
                'ulid'       => $refreshToken,
                'user_id'    => $user->id,
                'is_blocked' => 0,
            ])
            ->assertDatabaseCount(RefreshToken::class, 1);

        // Делаем рефреш, обновляем токен
        $response = $this->postJson('api/v1/refresh', [
            'refreshToken' => $refreshToken,
        ]);
        $response
            ->assertOk()
            ->assertJsonStructure([
                'accessToken',
                'refreshToken',
            ]);
        // Новый рефреш токен
        $newRefreshToken = $response->json('refreshToken');

        $this
            ->assertDatabaseHas(RefreshToken::class, [
                'ulid'       => $refreshToken,
                'user_id'    => $user->id,
                'is_blocked' => 1,
            ])
            ->assertDatabaseHas(RefreshToken::class, [
                'ulid'       => $newRefreshToken,
                'user_id'    => $user->id,
                'is_blocked' => 0,
            ])
            ->assertDatabaseCount(RefreshToken::class, 2);

        // Делаем обновление старым рефреш токеном, в результате блокируется вся цепочка
        $response = $this->postJson('api/v1/refresh', [
            'refreshToken' => $refreshToken,
        ]);
        $response
            ->assertUnauthorized()
            ->assertJson([
                'message' => 'Сессия была заблокирована',
            ]);

        $this
            ->assertDatabaseMissing(RefreshToken::class, [
                'user_id'    => $user->id,
                'is_blocked' => 0,
            ])
            ->assertDatabaseCount(RefreshToken::class, 2);
    }

    /**
     * Получаем рефреш токен, выполняя вход в аккаунт
     */
    private function getRefreshToken(string $email, string $password): string
    {
        return $this->postJson('api/v1/login', [
            'email'    => $email,
            'password' => $password,
        ])->json('refreshToken');
    }
}
