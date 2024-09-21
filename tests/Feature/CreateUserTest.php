<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Parents\Test;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;

final class CreateUserTest extends Test
{
    public function testCreateUser(): void
    {
        // Создаем нового пользователя
        $this
            ->postJson('api/v1/users', [
                'nickname'              => $nickname = Str::random(),
                'email'                 => $email = Str::random() . '@example.com',
                'password'              => $password = Str::password(10),
                'password_confirmation' => $password,
            ])
            ->assertCreated()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'nickname',
                    'avatar_url'
                ],
            ])
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('data.id')
                ->where('data.nickname', $nickname)
                ->where('data.avatar_url', null)
            );

        $this->assertDatabaseHas(User::class, [
            'nickname' => $nickname,
            'email'    => $email,
        ]);


    }

    public function testCreateUserWithNonUniqueData()
    {
        /** @var User $user */
        $user = User::factory()->create();

        // Пробуем создать пользователя с такими же данными
        $this
            ->postJson('api/v1/users', [
                'nickname'              => $user->nickname,
                'email'                 => $user->email,
                'password'              => $password = Str::password(10),
                'password_confirmation' => $password,
            ])
            ->assertUnprocessable()
            ->assertJson([
                'message' => 'Имя пользователя уже используется'
            ]);
    }
}
