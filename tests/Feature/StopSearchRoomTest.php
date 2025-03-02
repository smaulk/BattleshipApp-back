<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Parents\Test;
use App\Services\RoomQueueService;
use Illuminate\Support\Facades\Redis;

final class StopSearchRoomTest extends Test
{
    private const QUEUE = "queue:rooms";

    public function testStopSearchRoom(): void
    {
        $this->fakeEventWithModel();

        /** @var User $user1 */
        $user1 = User::factory()->create();
        $accessToken1 = $this->jwt->createToken($user1);

        (new RoomQueueService())->enqueue($user1->id);
        $this->assertTrue(Redis::llen(self::QUEUE) === 1);

        $this
            ->deleteJson('/api/v1/rooms/search', [], [
                'Authorization' => 'Bearer ' . $accessToken1,
            ])
            ->assertNoContent();

        $this->assertTrue(Redis::llen(self::QUEUE) === 0);
    }

    public function testStopSearchRoomNotRunning(): void
    {
        $this->fakeEventWithModel();

        /** @var User $user1 */
        $user1 = User::factory()->create();
        $accessToken1 = $this->jwt->createToken($user1);

        $this
            ->deleteJson('/api/v1/rooms/search', [], [
                'Authorization' => 'Bearer ' . $accessToken1,
            ])
            ->assertNoContent();

        $this->assertTrue(Redis::llen(self::QUEUE) === 0);
    }
}