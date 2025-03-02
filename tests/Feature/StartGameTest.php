<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\GameStatus;
use App\Events\CreateGame;
use App\Models\Game;
use App\Models\User;
use App\Parents\Test;
use App\Services\CreateRoomService;
use App\Services\StartGameService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Redis;

final class StartGameTest extends Test
{
    public function testStartGameOne(): void
    {
        $this->fakeEventWithModel();

        /** @var User $user1 */
        /** @var User $user2 */
        [$user1, $user2] = [User::factory()->create(), User::factory()->create()];
        $accessToken1 = $this->jwt->createToken($user1);

        $roomId = $this->createRoom($user1->id, $user2->id);

        $this
            ->putJson("/api/v1/rooms/{$roomId}", [], [
                'Authorization' => 'Bearer ' . $accessToken1,
            ])
            ->assertNoContent();

        $members = Redis::hgetall("rooms:$roomId");
        $this->assertTrue($members[$user1->id] == 1);
        Event::assertNotDispatched(CreateGame::class);
    }

    public function testStartGameBoth(): void
    {
        $this->fakeEventWithModel();

        /** @var User $user1 */
        /** @var User $user2 */
        [$user1, $user2] = [User::factory()->create(), User::factory()->create()];
        $accessToken2 = $this->jwt->createToken($user2);

        $roomId = $this->createRoom($user1->id, $user2->id);

        (new StartGameService())->run($user1->id, $roomId);
        $members = Redis::hgetall("rooms:$roomId");
        $this->assertTrue($members[$user1->id] == 1);

        // Отправляем запрос второго пользователя
        $this
            ->putJson("/api/v1/rooms/{$roomId}", [], [
                'Authorization' => 'Bearer ' . $accessToken2,
            ])
            ->assertNoContent();

        $this->assertEmpty(Redis::hgetall("rooms:$roomId"));

        $this->assertDatabaseHas(Game::class, [
            'uid1'   => $user2->id,
            'uid2'   => $user1->id,
            'status' => GameStatus::CREATED,
        ]);
        Event::assertDispatched(CreateGame::class);
    }

    public function testStartGameForbidden(): void
    {
        $this->fakeEventWithModel();

        /** @var User $user1 */
        /** @var User $user2 */
        /** @var User $user3 */
        [$user1, $user2, $user3] = [User::factory()->create(), User::factory()->create(), User::factory()->create()];
        $accessToken3 = $this->jwt->createToken($user3);

        $roomId = $this->createRoom($user1->id, $user2->id);
        $this
            ->putJson("/api/v1/rooms/{$roomId}", [], [
                'Authorization' => 'Bearer ' . $accessToken3,
            ])
            ->assertForbidden();

        $members = Redis::hgetall("rooms:$roomId");
        $this->assertFalse(in_array($user3->id, array_keys($members)));
        Event::assertNotDispatched(CreateGame::class);
    }

    public function testStartGameNotFound(): void
    {
        $this->fakeEventWithModel();

        /** @var User $user1 */
        $user1 = User::factory()->create();
        $accessToken1 = $this->jwt->createToken($user1);

        $this
            ->putJson("/api/v1/rooms/TEST", [], [
                'Authorization' => 'Bearer ' . $accessToken1,
            ])
            ->assertNotFound();

        Event::assertNotDispatched(CreateGame::class);
    }

    private function createRoom(int $uid1, int $uid2): string
    {
        $roomId = (new CreateRoomService())->run($uid1, $uid2);
        $members = Redis::hgetall("rooms:$roomId");

        $this->assertNotEmpty($members);
        $keys = array_keys($members);
        $this->assertTrue(in_array($uid1, $keys) && in_array($uid2, $keys));
        $this->assertTrue($members[$uid1] == 0 && $members[$uid2] == 0);

        return $roomId;
    }
}