<?php

namespace Tests\Feature;

use App\Enums\FriendshipStatus;
use App\Events\SendNotify;
use App\Models\Friendship;
use App\Models\User;
use App\Parents\Test;
use Illuminate\Support\Facades\Event;

final class AcceptFriendshipTest extends Test
{
    public function testAcceptFriendship(): void
    {
        $this->fakeEventWithModel();

        /** @var User $user1 */
        /** @var User $user2 */
        [$user1, $user2] = [User::factory()->create(), User::factory()->create()];
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
            ->putJson("/api/v1/friendships/{$user1->id}", [], [
                'Authorization' => "Bearer $accessToken2",
            ])
            ->assertNoContent();

        // Проверяем, что статус изменился
        $this->assertDatabaseHas(Friendship::class, [
            'uid1'   => $user1->id,
            'uid2'   => $user2->id,
            'status' => FriendshipStatus::FRIEND,
        ]);

        Event::assertDispatched(SendNotify::class);
    }

    public function testAcceptFriendshipFromOutUser(): void
    {
        $this->fakeEventWithModel();

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
            ->putJson("/api/v1/friendships/{$user2->id}", [], [
                'Authorization' => "Bearer $accessToken1",
            ])
            ->assertBadRequest()
            ->assertJson([
                'message' => 'Нельзя принять отправленную заявку'
            ]);

        // Проверяем, что статус не изменился
        $this->assertDatabaseHas(Friendship::class, [
            'uid1'   => $user1->id,
            'uid2'   => $user2->id,
            'status' => FriendshipStatus::REQ_UID1,
        ]);

        Event::assertNotDispatched(SendNotify::class);
    }
}
