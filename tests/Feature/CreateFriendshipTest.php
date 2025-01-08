<?php

namespace Tests\Feature;

use App\Enums\FriendshipStatus;
use App\Models\User;
use App\Parents\Test;
use Illuminate\Support\Facades\Notification;

final class CreateFriendshipTest extends Test
{
    public function testCreateFriendship(): void
    {
        /** @var User $user1 */
        $user1 = User::factory()->create();
        $accessToken = $this->jwt->createToken($user1);
        /** @var User $user2 */
        $user2 = User::factory()->create();

        // Создаем дружбу между пользователями
        $this
            ->postJson('/api/v1/friendships', [
                'friendId' => $user2->getKey(),
            ], [
                'Authorization' => "Bearer $accessToken",
            ])
            ->assertCreated();
        $this->assertDatabaseCount('friendships', 1);

        // Пробуем еще раз создать дружбу между пользователями
        $this
            ->postJson('/api/v1/friendships', [
                'friendId' => $user2->getKey(),
            ], [
                'Authorization' => "Bearer $accessToken",
            ])
            ->assertBadRequest()
            ->assertJson([
                'message' => 'Запись уже существует'
            ]);

        // Пробуем создать дружбу пользователя с самим собой
        $this
            ->postJson('/api/v1/friendships', [
                'friendId' => $user1->getKey(),
            ], [
                'Authorization' => "Bearer $accessToken",
            ])
            ->assertBadRequest()
            ->assertJson([
                'message' => 'Идентификаторы пользователей совпадают'
            ]);


        // Пробуем создать дружбу с несуществующим пользователем
        $this
            ->postJson('/api/v1/friendships', [
                'friendId' => $user2->getKey()*2,
            ], [
                'Authorization' => "Bearer $accessToken",
            ])
            ->assertBadRequest()
            ->assertJson([
                'message' => 'Пользователь не найден'
            ]);
    }
}
