<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Parents\Test;
use Illuminate\Testing\Fluent\AssertableJson;

final class FindUserTest extends Test
{
    public function testFindUser(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        // Получаем данные пользователя
        $this
            ->get("/api/v1/users/$user->id")
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'nickname',
                    'avatar_url'
                ],
            ])
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('data.id', $user->id)
                ->where('data.nickname', $user->nickname)
                ->where('data.avatar_url', null)
            );
    }

    public function testFindNonExistentUser(): void
    {
        $this
            ->get("/api/v1/users/1")
            ->assertNotFound()
            ->assertJson([
                'message' => 'Пользователь не найден'
            ]);
    }
}