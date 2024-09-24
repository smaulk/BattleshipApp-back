<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Parents\Test;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;

final class UpdateUserTest extends Test
{
    public function testUpdateUser(): void
    {
        // Создаем пользователя (с заполенным email_verified_at)
        /** @var User $user */
        $user = User::factory()->create();
        $accessToken = $this->jwt->createToken($user);
        // Проверяем, что почта не подтверждена
        $this->assertNotNull($user->email_verified_at);

        // Обновляем данные пользователя
        $this
            ->putJson("/api/v1/users/$user->id", [
                'nickname' => $nickname = Str::random(),
                'email'    => $email = Str::random() . '@example.com',
            ], [
                'Authorization' => 'Bearer ' . $accessToken,
            ])
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'nickname',
                    'avatarUrl'
                ],
            ])
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('data.id', $user->id)
                ->where('data.nickname', $nickname)
                ->where('data.avatarUrl', null)
            );

        // Проверяем, что данные пользователя обновлись, а почта стала "не подтвержденной"
        $this->assertDatabaseHas('users', [
            'id'                => $user->id,
            'nickname'          => $nickname,
            'email'             => $email,
            'email_verified_at' => null,
        ]);
    }

    public function testUpdateUserWithNonUniqueData(): void
    {
        /** @var User $user1 */
        $user1 = User::factory()->create();

        /** @var User $user2 */
        $user2 = User::factory()->create();
        $accessToken = $this->jwt->createToken($user2);

        // Отправляем запрос на обновление пользователя 2, с данными пользователя 1
        $this
            ->putJson("/api/v1/users/$user2->id", [
                'nickname' => $user1->nickname,
                'email'    => $user1->email,
            ], [
                'Authorization' => 'Bearer ' . $accessToken,
            ])
            ->assertUnprocessable()
            ->assertJson([
                'message' => 'Имя пользователя уже используется'
            ]);
    }
}