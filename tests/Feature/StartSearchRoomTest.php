<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Events\CreateRoom;
use App\Models\User;
use App\Parents\Test;
use App\Services\RoomQueueService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Redis;

final class StartSearchRoomTest extends Test
{
    private const QUEUE = "queue:rooms";

    public function testStartSearchRoom(): void
    {
        $this->fakeEventWithModel();

        /** @var User $user1 */
        $user1 = User::factory()->create();
        $accessToken1 = $this->jwt->createToken($user1);

        $this
            ->postJson('/api/v1/room-queue', [], [
                'Authorization' => 'Bearer ' . $accessToken1,
            ])
            ->assertNoContent();

        // Второй раз запускаем поиск
        $this
            ->postJson('/api/v1/room-queue', [], [
                'Authorization' => 'Bearer ' . $accessToken1,
            ])
            ->assertNoContent();

        $this->assertTrue(Redis::llen(self::QUEUE) === 1);
        $playerId = (int)Redis::lpop(self::QUEUE);
        $this->assertEquals($user1->id, $playerId);
        Event::assertNotDispatched(CreateRoom::class);
    }

    public function testEndSearchRoom(): void
    {
        $this->fakeEventWithModel();

        /** @var User $user1 */
        /** @var User $user2 */
        [$user1, $user2] = [User::factory()->create(), User::factory()->create()];
        $accessToken2 = $this->jwt->createToken($user2);

        (new RoomQueueService())->enqueue($user1->id);

        $this->assertTrue(Redis::llen(self::QUEUE) === 1);

        $this
            ->postJson('/api/v1/room-queue', [], [
                'Authorization' => 'Bearer ' . $accessToken2,
            ])
            ->assertNoContent();

        $this->assertTrue(Redis::llen(self::QUEUE) === 0);

        $this->assertNotEmpty(Redis::keys('rooms:*'));
        Event::assertDispatched(CreateRoom::class);
    }
}