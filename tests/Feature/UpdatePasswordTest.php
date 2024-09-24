<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Parents\Test;
use Illuminate\Support\Facades\Hash;

final class UpdatePasswordTest extends Test
{
    public function testUpdatePassword(): void
    {
        $pass1 = 'password1';
        $pass2 = 'password2';
        /** @var User $user */
        $user = User::factory()->create([
            'password' => $pass1,
        ]);
        $accessToken = $this->jwt->createToken($user);

        // Обновляем пароль
        $this
            ->putJson("api/v1/users/$user->id/password", [
                'currentPassword'         => $pass1,
                'newPassword'             => $pass2,
                'newPasswordConfirmation' => $pass2,
            ], [
                'Authorization' => "Bearer $accessToken",
            ])
            ->assertNoContent();

        // Проверяем, что пароль изменился
        $this->assertTrue(Hash::check($pass2, $user->fresh()?->password));
    }

    public function testUpdatePasswordWithWrongData(): void
    {
        /** @var User $user */
        $pass = 'password1';
        $user = User::factory()->create([
            'password' => $pass,
        ]);
        $accessToken = $this->jwt->createToken($user);

        // Обновляем пароль, вводя неверный текущий пароль
        $this
            ->putJson("api/v1/users/$user->id/password", [
                'currentPassword'         => 'wrong-password111',
                'newPassword'             => 'password2',
                'newPasswordConfirmation' => 'password2',
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
                'currentPassword'         => $pass,
                'newPassword'             => $pass,
                'newPasswordConfirmation' => $pass,
            ], [
                'Authorization' => "Bearer $accessToken",
            ])
            ->assertUnprocessable()
            ->assertJson([
                'message' => 'Новый пароль не должен совпадать с текущим'
            ]);
    }
}