<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\GameStatus;
use App\Models\Game;
use App\Models\User;
use App\Parents\Test;
use Illuminate\Testing\Fluent\AssertableJson;

final class GetUserStatisticTest extends Test
{
    public function testGetUserStatistic(): void
    {
        /** @var User $user1 */
        /** @var User $user2 */
        [$user1, $user2] = [User::factory()->create(), User::factory()->create()];

        Game::create([
            'uid1'   => $user1->id,
            'uid2'   => $user2->id,
            'status' => GameStatus::WIN_UID1
        ]);

        $this
            ->getJson("/api/v1/users/{$user1->id}/statistic")
            ->assertOk()
            ->assertJsonStructure([
                'data' => ['games', 'wins', 'losses', 'draws', 'points',]
            ])
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('data.games', 1)
                ->where('data.wins', 1)
                ->where('data.losses', 0)
                ->where('data.draws', 0)
                ->where('data.points', 10)
            );

        $this
            ->getJson("/api/v1/users/{$user2->id}/statistic")
            ->assertOk()
            ->assertJsonStructure([
                'data' => ['games', 'wins', 'losses', 'draws', 'points',]
            ])
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('data.games', 1)
                ->where('data.wins', 0)
                ->where('data.losses', 1)
                ->where('data.draws', 0)
                ->where('data.points', 0)
            );
    }
}