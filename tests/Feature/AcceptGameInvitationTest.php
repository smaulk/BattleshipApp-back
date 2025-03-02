<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Events\CreateRoom;
use App\Models\GameInvitation;
use App\Models\User;
use App\Parents\Test;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Redis;

final class AcceptGameInvitationTest extends Test
{
    public function testAcceptInvite(): void
    {
        $this->fakeEventWithModel();

        /** @var User $user1 */
        /** @var User $user2 */
        [$user1, $user2] = [User::factory()->create(), User::factory()->create()];
        $accessToken2 = $this->jwt->createToken($user2);

        GameInvitation::create([
            'sender_id'   => $user1->id,
            'receiver_id' => $user2->id,
            'invited_at'  => now(),
        ]);

        $this
            ->putJson("/api/v1/invites/{$user1->id}", [], [
                'Authorization' => "Bearer $accessToken2",
            ])
            ->assertNoContent();

        $this->assertDatabaseEmpty(GameInvitation::class);
        $this->assertNotEmpty(Redis::keys('rooms:*'));
        Event::assertDispatched(CreateRoom::class);
    }

    public function testAcceptInviteNotFound(): void
    {
        $this->fakeEventWithModel();

        /** @var User $user1 */
        /** @var User $user2 */
        [$user1, $user2] = [User::factory()->create(), User::factory()->create()];
        $accessToken2 = $this->jwt->createToken($user2);

        $this
            ->putJson("/api/v1/invites/{$user1->id}", [], [
                'Authorization' => "Bearer $accessToken2",
            ])
            ->assertNotFound();

        $this->assertEmpty(Redis::keys('rooms:*'));
        Event::assertNotDispatched(CreateRoom::class);
    }

    public function testAcceptInviteSent(): void
    {
        $this->fakeEventWithModel();

        /** @var User $user1 */
        /** @var User $user2 */
        [$user1, $user2] = [User::factory()->create(), User::factory()->create()];
        $accessToken1 = $this->jwt->createToken($user1);

        GameInvitation::create([
            'sender_id'   => $user1->id,
            'receiver_id' => $user2->id,
            'invited_at'  => now(),
        ]);

        $this
            ->putJson("/api/v1/invites/{$user2->id}", [], [
                'Authorization' => "Bearer $accessToken1",
            ])
            ->assertNotFound();

        $this->assertEmpty(Redis::keys('rooms:*'));
        Event::assertNotDispatched(CreateRoom::class);
    }
}