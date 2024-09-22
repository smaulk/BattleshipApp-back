<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use App\Parents\Test;
use Illuminate\Support\Facades\Notification;

final class ForgotPasswordTest extends Test
{
    public function testForgotPassword(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Notification::fake();

        // Отправляем запрос, на отправку письма для сброса пароля
        $this
            ->post('/api/v1/password/forgot', [
                'email' => $user->email,
            ])
            ->assertNoContent();

        // Проверка, что уведомление срабротало и письмо отправлено
        Notification::assertCount(1);
        Notification::assertSentTo($user, ResetPasswordNotification::class);

        // Проверка слишком частой отправки запроса
        $this
            ->post('/api/v1/password/forgot', [
                'email' => $user->email,
            ])
            ->assertTooManyRequests()
            ->assertJson([
                'message' => 'Слишком много запросов на сброс пароля'
            ]);

        Notification::assertCount(1);
    }

    public function testForgotPasswordWithWrongEmail(): void
    {
        Notification::fake();
        // Отправляем запрос, на отправку письма для сброса пароля, с несуществующей почтой
        $this
            ->post('/api/v1/password/forgot', [
                'email' => 'wrong@mail.ru',
            ])
            ->assertNotFound()
            ->assertJson([
                'message' => 'Пользователь с данной электронной почтой не найден'
            ]);

        // Проверка, что ничего не было отправлено
        Notification::assertNothingSent();
    }
}