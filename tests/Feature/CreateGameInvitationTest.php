<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\FriendshipStatus;
use App\Events\SendNotify;
use App\Models\Friendship;
use App\Models\GameInvitation;
use App\Models\User;
use App\Parents\Test;
use Illuminate\Support\Facades\Event;

final class CreateGameInvitationTest extends Test
{
    public function testCreateInvite(): void
    {
        $this->fakeEventWithModel();

        /** @var User $user1 */
        /** @var User $user2 */
        [$user1, $user2] = [User::factory()->create(), User::factory()->create()];
        $accessToken1 = $this->jwt->createToken($user1);

        Friendship::create([
            'uid1' => $user1->id,
            'uid2' => $user2->id,
            'status' => FriendshipStatus::FRIEND
        ]);

        $this
            ->postJson('/api/v1/invites', [
                'friendId' => $user2->id
            ], [
                'Authorization' => "Bearer $accessToken1",
            ])
            ->assertCreated();

        /** @var GameInvitation $invite1 */
        $invite1 = GameInvitation::findByUsers($user1->id, $user2->id)->first();
        $this->assertNotNull($invite1);

        // Отправляем еще раз приглашение с задержкой
        sleep(1);
        $this
            ->postJson('/api/v1/invites', [
                'friendId' => $user2->id
            ], [
                'Authorization' => "Bearer $accessToken1",
            ])
            ->assertCreated();

        /** @var GameInvitation $invite2 */
        $invite2 = GameInvitation::findByUsers($user1->id, $user2->id)->first();
        $this->assertNotNull($invite2);

        $this->assertNotEquals($invite1->invited_at->getTimestamp(), $invite2->invited_at->getTimestamp());

        Event::assertDispatchedTimes(SendNotify::class, 2);
    }

    public function testCreateInviteForNotFriends(): void
    {
        $this->fakeEventWithModel();

        /** @var User $user1 */
        /** @var User $user2 */
        [$user1, $user2] = [User::factory()->create(), User::factory()->create()];
        $accessToken1 = $this->jwt->createToken($user1);

        $this
            ->postJson('/api/v1/invites', [
                'friendId' => $user2->id
            ], [
                'Authorization' => "Bearer $accessToken1",
            ])
            ->assertBadRequest();

        $this->assertDatabaseEmpty(GameInvitation::class);

        Event::assertNotDispatched(SendNotify::class);
    }
}