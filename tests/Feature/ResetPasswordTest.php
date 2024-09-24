<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Parents\Test;
use App\Services\ForgotPasswordService;
use Illuminate\Auth\Passwords\PasswordBrokerManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

final class ResetPasswordTest extends Test
{
    public function testResetPassword(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $token = Password::createToken($user);
        $this->assertDatabaseCount('password_reset_tokens', 1);

        $password = 'password123';
        $this
            ->postJson('/api/v1/password/reset', [
                'token'                 => $token,
                'email'                 => $user->email,
                'password'              => $password,
                'passwordConfirmation' => $password,
            ])
            ->assertNoContent();

        $this->assertTrue(Hash::check($password, $user->fresh()?->password));
    }

    public function testResetPasswordWithWrongEmail(): void
    {
        $password = 'password123';
        // Отправлям запрос с не существующей почтой
        $this
            ->postJson('/api/v1/password/reset', [
                'token'                 => 'token',
                'email'                 => 'wrong@mail.ru',
                'password'              => $password,
                'passwordConfirmation' => $password,
            ])
            ->assertNotFound()
            ->assertJson([
                'message' => 'Пользователь с данной электронной почтой не найден'
            ]);
    }

    public function testResetPasswordWithWrongToken(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $password = 'password123';
        // Отправялем запрос с неверным токеном
        $this
            ->postJson('/api/v1/password/reset', [
                'token'                 => 'token',
                'email'                 => $user->email,
                'password'              => $password,
                'passwordConfirmation' => $password,
            ])
            ->assertBadRequest()
            ->assertJson([
                'message' => 'Недействительный токен сброса пароля'
            ]);
    }
}