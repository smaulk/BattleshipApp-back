<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Parents\Test;
use Illuminate\Support\Facades\Hash;

final class UpdateUserPasswordTest extends Test
{
    public function testUpdateUserPassword(): void
    {
        $pass1 = 'password1';
        $pass2 = 'password2';
        /** @var User $user */
        $user = User::factory()->create([
            'password' => Hash::make($pass1),
        ]);
        $accessToken = $this->jwt->createToken($user);

        // Обновляем пароль
        $this
            ->putJson("api/v1/users/$user->id/password", [
                'current_password'          => $pass1,
                'new_password'              => $pass2,
                'new_password_confirmation' => $pass2,
            ], [
                'Authorization' => "Bearer $accessToken",
            ])
            ->assertNoContent();

        // Проверяем, что пароль изменился
        $this->assertTrue(Hash::check($pass2, $user->fresh()?->password));
    }

    public function testUpdateUserPasswordWithError(): void
    {
        /** @var User $user */
        $pass = 'password1';
        $user = User::factory()->create([
            'password' => Hash::make($pass),
        ]);
        $accessToken = $this->jwt->createToken($user);

        // Обновляем пароль, вводя неверный текущий пароль
        $this
            ->putJson("api/v1/users/$user->id/password", [
                'current_password'          => 'wrong-password111',
                'new_password'              => 'password2',
                'new_password_confirmation' => 'password2',
            ], [
                'Authorization' => "Bearer $accessToken",
            ])
            ->assertUnprocessable()
            ->assertJson([
                'message' => 'Неверный текущий пароль'
            ]);

        // Обновляем пароль, вводя такой же новый пароль
        $this
            ->putJson("api/v1/users/$user->id/password", [
                'current_password'          => $pass,
                'new_password'              => $pass,
                'new_password_confirmation' => $pass,
            ], [
                'Authorization' => "Bearer $accessToken",
            ])
            ->assertUnprocessable()
            ->assertJson([
                'message' => 'Новый пароль не должен совпадать с текущим'
            ]);
    }
}