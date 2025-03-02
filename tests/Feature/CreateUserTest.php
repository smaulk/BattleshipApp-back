<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserStatistic;
use App\Parents\Test;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;

final class CreateUserTest extends Test
{
    public function testCreateUser(): void
    {
        $this->fakeEventWithModel();

        // Создаем нового пользователя
        $response = $this
            ->postJson('api/v1/users', [
                'nickname'             => $nickname = Str::random(),
                'email'                => $email = Str::random() . '@example.com',
                'password'             => $password = Str::password(10),
                'passwordConfirmation' => $password,
            ])
            ->assertCreated()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'nickname',
                    'avatarUrl'
                ],
            ])
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('data.id')
                ->where('data.nickname', $nickname)
                ->where('data.avatarUrl', null)
            );

        $userId = $response->json('data.id');

        $this->assertDatabaseHas(User::class, [
            'id'       => $userId,
            'nickname' => $nickname,
            'email'    => $email,
        ]);

        $this->assertDatabaseHas(UserStatistic::class, [
            'user_id' => $userId,
        ]);
        Notification::assertCount(1);
    }

    public function testCreateUserWithNonUniqueData()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->fakeEventWithModel();

        // Пробуем создать пользователя с такими же данными
        $this
            ->postJson('api/v1/users', [
                'nickname'             => $user->nickname,
                'email'                => $user->email,
                'password'             => $password = Str::password(10),
                'passwordConfirmation' => $password,
            ])
            ->assertUnprocessable()
            ->assertJson([
                'message' => 'Имя пользователя уже используется'
            ]);

        Notification::assertNothingSent();
    }
}
