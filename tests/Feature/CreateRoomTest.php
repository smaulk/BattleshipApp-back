<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Parents\Test;
use Illuminate\Support\Facades\Redis;

final class CreateRoomTest extends Test
{
    public function testCreateRoom(): void
    {
        $this->fakeEventWithModel();

        /** @var User $user1 */
        $user1 = User::factory()->create();
        $accessToken1 = $this->jwt->createToken($user1);

        $this
            ->postJson('/api/v1/rooms', [], [
                'Authorization' => "Bearer $accessToken1",
            ])
            ->assertJsonStructure([
                'data' => [
                    'roomId'
                ],
            ])
            ->assertCreated();

        $this->assertNotEmpty(Redis::keys('rooms:*'));
    }
}