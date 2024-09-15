<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CreateUserTest extends TestCase
{
    public function testCreateUser(): void
    {
        //Создаем нового пользователя
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
                    'nickname'
                ]
            ])
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('data.id')
                ->where('data.nickname', $nickname)
            );

        $this->assertDatabaseHas(User::class, [
            'nickname'  => $nickname,
            'email' => $email,
        ]);

        //Пробуем создать пользователя с такими же данными
        $this
            ->postJson('api/v1/users', [
                'nickname'              => $nickname ,
                'email'                 => $email,
                'password'              => $password,
                'password_confirmation' => $password,
            ])
            ->assertUnprocessable()
            ->assertJson([
                'message' => 'Имя пользователя уже используется'
            ])
        ;
    }
}
