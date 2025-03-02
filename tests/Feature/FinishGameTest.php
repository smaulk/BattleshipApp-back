<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Dto\CreateGameDto;
use App\Dto\EndGameDto;
use App\Enums\GameStatus;
use App\Enums\GameType;
use App\Events\EndGame;
use App\Models\Game;
use App\Models\User;
use App\Parents\Test;
use App\Services\CreateGameService;
use App\Services\FinishGameService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Redis;

final class FinishGameTest extends Test
{
    public function testFinishGameOne(): void
    {
        $this->fakeEventWithModel();

        /** @var User $user1 */
        /** @var User $user2 */
        [$user1, $user2] = [User::factory()->create(), User::factory()->create()];
        $accessToken1 = $this->jwt->createToken($user1);

        $game = $this->createGame($user1->id, $user2->id);
        $type1 = GameType::WIN;

        $this
            ->putJson("/api/v1/games/{$game->id}", [
                'type' => $type1->value,
            ], [
                'Authorization' => 'Bearer ' . $accessToken1,
            ])
            ->assertNoContent();

        $key = "games:{$game->id}";
        $data = Redis::hgetall($key);
        $this->assertNotEmpty($data);

        $status = $type1->toStatus($game->uid1 === $user1->id);
        $this->assertTrue($data[$user1->id] == $status->value);
        Event::assertNotDispatched(EndGame::class);
    }

    public function testFinishGameBoth(): void
    {
        $this->fakeEventWithModel();

        /** @var User $user1 */
        /** @var User $user2 */
        [$user1, $user2] = [User::factory()->create(), User::factory()->create()];
        $accessToken2 = $this->jwt->createToken($user2);

        $game = $this->createGame($user1->id, $user2->id);
        $type1 = GameType::WIN;
        $type2 = GameType::LOSE;

        (new FinishGameService())->run(
            EndGameDto::fromArray([
                'gameId' => $game->id,
                'userId' => $user1->id,
                'type'   => $type1->value,
            ]),
        );

        $this
            ->putJson("/api/v1/games/{$game->id}", [
                'type' => $type2->value,
            ], [
                'Authorization' => 'Bearer ' . $accessToken2,
            ])
            ->assertNoContent();

        $key = "games:{$game->id}";
        $data = Redis::hgetall($key);
        $this->assertEmpty($data);

        $status = $type1->toStatus($game->uid1 === $user1->id);

        $this->assertDatabaseHas(Game::class, [
            'uid1'   => $user1->id,
            'uid2'   => $user2->id,
            'status' => $status,
        ]);
        Event::assertDispatched(EndGame::class);
    }

    public function testFinishGameStatusError(): void
    {
        $this->fakeEventWithModel();

        /** @var User $user1 */
        /** @var User $user2 */
        [$user1, $user2] = [User::factory()->create(), User::factory()->create()];
        $accessToken2 = $this->jwt->createToken($user2);

        $game = $this->createGame($user1->id, $user2->id);
        $type1 = GameType::WIN;
        $type2 = GameType::WIN;

        (new FinishGameService())->run(
            EndGameDto::fromArray([
                'gameId' => $game->id,
                'userId' => $user1->id,
                'type'   => $type1->value,
            ]),
        );

        $this
            ->putJson("/api/v1/games/{$game->id}", [
                'type' => $type2->value,
            ], [
                'Authorization' => 'Bearer ' . $accessToken2,
            ])
            ->assertBadRequest();

        $key = "games:{$game->id}";
        $data = Redis::hgetall($key);
        $this->assertNotEmpty($data);

        Event::assertDispatched(EndGame::class);
    }

    public function testFinishGameForbidden(): void
    {
        $this->fakeEventWithModel();

        /** @var User $user1 */
        /** @var User $user2 */
        /** @var User $user3 */
        [$user1, $user2, $user3] = [User::factory()->create(), User::factory()->create(), User::factory()->create()];
        $accessToken3 = $this->jwt->createToken($user3);

        $game = $this->createGame($user1->id, $user2->id);

        $this
            ->putJson("/api/v1/games/{$game->id}", [
                'type' => GameType::WIN,
            ], [
                'Authorization' => 'Bearer ' . $accessToken3,
            ])
            ->assertForbidden();

        $key = "games:{$game->id}";
        $data = Redis::hgetall($key);
        $this->assertEmpty($data);

        Event::assertNotDispatched(EndGame::class);
    }

    private function createGame(int $uid1, int $uid2): Game
    {
        $game = (new CreateGameService())->run(
            CreateGameDto::fromArray([
                'uid1' => $uid1,
                'uid2' => $uid2
            ])
        );

        $this->assertDatabaseHas(Game::class, [
            'uid1'   => $uid1,
            'uid2'   => $uid2,
            'status' => GameStatus::CREATED,
        ]);

        return $game;
    }
}