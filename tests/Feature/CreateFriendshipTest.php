<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Events\SendNotify;
use App\Models\User;
use App\Parents\Test;
use Illuminate\Support\Facades\Event;

final class CreateFriendshipTest extends Test
{
    public function testCreateFriendship(): void
    {
        $this->fakeEventWithModel();

        /** @var User $user1 */
        /** @var User $user2 */
        [$user1, $user2] = [User::factory()->create(), User::factory()->create()];
        $accessToken = $this->jwt->createToken($user1);

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

        Event::assertDispatched(SendNotify::class);
    }

    public function testCreateFriendshipError(): void
    {
        $this->fakeEventWithModel();

        /** @var User $user1 */
        /** @var User $user2 */
        [$user1, $user2] = [User::factory()->create(), User::factory()->create()];
        $accessToken = $this->jwt->createToken($user1);

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
            ->assertNotFound()
            ->assertJson([
                'message' => 'Пользователь не найден'
            ]);

        Event::assertNotDispatched(SendNotify::class);
    }
}
