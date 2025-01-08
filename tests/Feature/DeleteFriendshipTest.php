<?php

namespace Tests\Feature;

use App\Enums\FriendshipStatus;
use App\Models\User;
use App\Parents\Test;
use Illuminate\Support\Facades\DB;

final class DeleteFriendshipTest extends Test
{
    public function testDeleteFriendshipFromIn(): void
    {
        /** @var User $user1 */
        $user1 = User::factory()->create();
        /** @var User $user2 */
        $user2 = User::factory()->create();
        $accessToken2 = $this->jwt->createToken($user2);
        // Создаем дружбу между пользователями
        DB::table('friendships')->insert([
            'uid1' => $user1->id,
            'uid2' => $user2->id,
            'status' => FriendshipStatus::REQ_UID1,
        ]);
        $this->assertDatabaseCount('friendships', 1);

        // Отклоняем полученную заявку в друзья
        $this
            ->deleteJson('/api/v1/friendships/' . $user1->getKey(), [], [
                'Authorization' => "Bearer $accessToken2",
            ])
            ->assertNoContent();
        // Проверяем, что запись удалена
        $this->assertDatabaseEmpty('friendships');
    }

    public function testDeleteFriendshipFromOut(): void
    {
        /** @var User $user1 */
        $user1 = User::factory()->create();
        $accessToken1 = $this->jwt->createToken($user1);
        /** @var User $user2 */
        $user2 = User::factory()->create();

        // Создаем дружбу между пользователями
        DB::table('friendships')->insert([
            'uid1' => $user1->id,
            'uid2' => $user2->id,
            'status' => FriendshipStatus::REQ_UID1,
        ]);
        $this->assertDatabaseCount('friendships', 1);

        // Отменяем отправленную заявку в друзья
        $this
            ->deleteJson('/api/v1/friendships/' . $user2->getKey(), [], [
                'Authorization' => "Bearer $accessToken1",
            ])
            ->assertNoContent();
        // Проверяем, что запись удалена
        $this->assertDatabaseEmpty('friendships');
    }

    public function testDeleteFriendshipFromFriends(): void
    {
        /** @var User $user1 */
        $user1 = User::factory()->create();
        $accessToken1 = $this->jwt->createToken($user1);
        /** @var User $user2 */
        $user2 = User::factory()->create();

        // Создаем дружбу между пользователями
        DB::table('friendships')->insert([
            'uid1' => $user1->id,
            'uid2' => $user2->id,
            'status' => FriendshipStatus::FRIEND,
        ]);
        $this->assertDatabaseCount('friendships', 1);

        // Удаляем пользователя из друзей
        $this
            ->deleteJson('/api/v1/friendships/' . $user2->getKey(), [], [
                'Authorization' => "Bearer $accessToken1",
            ])
            ->assertNoContent();
        // Проверяем, что запись удалена
        $this->assertDatabaseEmpty('friendships');
    }
}
