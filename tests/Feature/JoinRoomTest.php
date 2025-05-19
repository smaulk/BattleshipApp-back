<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Parents\Test;
use App\Services\CreateRoomService;
use Illuminate\Support\Facades\Redis;

final class JoinRoomTest extends Test
{
    public function testJoinRoom(): void
    {
        $this->fakeEventWithModel();

        /** @var User $user1 */
        /** @var User $user2 */
        [$user1, $user2] = [User::factory()->create(), User::factory()->create()];
        $accessToken2 = $this->jwt->createToken($user2);

        $roomId = (new CreateRoomService())->run($user1->id);

        $this
            ->postJson("/api/v1/rooms/{$roomId}/join", [], [
                'Authorization' => 'Bearer ' . $accessToken2,
            ])
            ->assertJsonStructure([
                'data' => [
                    'roomTtl',
                ],
            ])
            ->assertOk();

        $roomKey = "rooms:$roomId";
        $members = Redis::hkeys($roomKey);
        $this->assertTrue(in_array($user1->id, $members));
        $this->assertTrue(in_array($user2->id, $members));
    }

    public function testJoinFilledRoom(): void
    {
        $this->fakeEventWithModel();

        /** @var User $user1 */
        /** @var User $user2 */
        /** @var User $user3 */
        [$user1, $user2, $user3] = [User::factory()->create(), User::factory()->create(), User::factory()->create()];
        $accessToken3 = $this->jwt->createToken($user3);

        $roomId = (new CreateRoomService())->run($user1->id, $user2->id);

        $this
            ->postJson("/api/v1/rooms/{$roomId}/join", [], [
                'Authorization' => 'Bearer ' . $accessToken3,
            ])
            ->assertForbidden();

        $roomKey = "rooms:$roomId";
        $members = Redis::hkeys($roomKey);
        $this->assertTrue(in_array($user1->id, $members));
        $this->assertTrue(in_array($user2->id, $members));
        $this->assertFalse(in_array($user3->id, $members));
    }

    public function testJoinRoomNotFound(): void
    {
        $this->fakeEventWithModel();

        /** @var User $user1 */
        $user1 = User::factory()->create();
        $accessToken1 = $this->jwt->createToken($user1);

        $this
            ->postJson("/api/v1/rooms/TEST/join", [], [
                'Authorization' => 'Bearer ' . $accessToken1,
            ])
            ->assertNotFound();
    }
}