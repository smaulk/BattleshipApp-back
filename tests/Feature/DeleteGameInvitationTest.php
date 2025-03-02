<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\GameInvitation;
use App\Models\User;
use App\Parents\Test;

final class DeleteGameInvitationTest extends Test
{
    public function testDeclineGameInvitation(): void
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
            ->deleteJson("/api/v1/invites/{$user1->id}", [], [
                'Authorization' => "Bearer $accessToken2",
            ])
            ->assertNoContent();

        $this->assertDatabaseEmpty(GameInvitation::class);
    }

    public function testCancelGameInvitation(): void
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
            ->deleteJson("/api/v1/invites/{$user2->id}", [], [
                'Authorization' => "Bearer $accessToken1",
            ])
            ->assertNoContent();

        $this->assertDatabaseEmpty(GameInvitation::class);
    }

    public function testDeleteGameInvitationNotFound(): void
    {
        $this->fakeEventWithModel();

        /** @var User $user1 */
        /** @var User $user2 */
        [$user1, $user2] = [User::factory()->create(), User::factory()->create()];
        $accessToken1 = $this->jwt->createToken($user1);

        $this
            ->deleteJson("/api/v1/invites/{$user2->id}", [], [
                'Authorization' => "Bearer $accessToken1",
            ])
            ->assertNoContent();

        $this->assertDatabaseEmpty(GameInvitation::class);
    }
}