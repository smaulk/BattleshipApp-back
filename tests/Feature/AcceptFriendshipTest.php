<?php

namespace Tests\Feature;

use App\Enums\FriendshipStatus;
use App\Models\Friendship;
use App\Models\User;
use App\Parents\Test;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class AcceptFriendshipTest extends Test
{
    public function testAcceptFriendship(): void
    {
        /** @var User $user1 */
        $user1 = User::factory()->create();
        /** @var User $user2 */
        $user2 = User::factory()->create();
        $accessToken2 = $this->jwt->createToken($user2);
        // Создаем дружбу между пользователями
        Friendship::create([
            'uid1' => $user1->id,
            'uid2' => $user2->id,
            'status' => FriendshipStatus::REQ_UID1,
        ]);
        $this->assertDatabaseCount('friendships', 1);

        // Принимаем дружбу от первого пользователя вторым пользователем
        $this
            ->putJson('/api/v1/friendships/' . $user1->getKey(), [], [
                'Authorization' => "Bearer $accessToken2",
            ])
            ->assertNoContent();

        // Проверяем, что статус изменился
        $this->assertDatabaseHas('friendships', [
            'uid1'   => $user1->id,
            'uid2'   => $user2->id,
            'status' => FriendshipStatus::FRIEND,
        ]);
    }

    public function testAcceptFriendshipFromOutUser(): void
    {
        /** @var User $user1 */
        $user1 = User::factory()->create();
        $accessToken1 = $this->jwt->createToken($user1);
        /** @var User $user2 */
        $user2 = User::factory()->create();

        // Создаем дружбу между пользователями
        Friendship::create([
            'uid1' => $user1->id,
            'uid2' => $user2->id,
            'status' => FriendshipStatus::REQ_UID1,
        ]);
        $this->assertDatabaseCount('friendships', 1);

        // Пробуем принять дружбу тем же пользователем
        $this
            ->putJson('/api/v1/friendships/' . $user2->getKey(), [], [
                'Authorization' => "Bearer $accessToken1",
            ])
            ->assertBadRequest()
            ->assertJson([
                'message' => 'Нельзя принять отправленную заявку'
            ]);

        // Проверяем, что статус не изменился
        $this->assertDatabaseHas('friendships', [
            'uid1'   => $user1->id,
            'uid2'   => $user2->id,
            'status' => FriendshipStatus::REQ_UID1,
        ]);
    }
}
