<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use App\Parents\Test;
use Illuminate\Support\Facades\Notification;

final class SendEmailVerificationTest extends Test
{
    public function testSendEmailVerification(): void
    {
        // Пользователь, с не подтвержденной почтой
        /** @var User $user */
        $user = User::factory()->unverified()->create();
        $accessToken = $this->jwt->createToken($user);
        Notification::fake();

        // Отправляем запрос, на отправку письма для подтверждения почты
        $this
            ->post("/api/v1/users/$user->id/email-verification/send", [], [
                'Authorization' => 'Bearer ' . $accessToken,
            ])
            ->assertNoContent();

        // Проверка, что уведомление срабротало и письмо отправлено
        Notification::assertCount(1);
        Notification::assertSentTo($user, VerifyEmailNotification::class);
    }

    public function testSendEmailVerificationWithVerifiedUser(): void
    {
        // Пользователь, с подтвержденной почтой
        /** @var User $user */
        $user = User::factory()->create();
        $accessToken = $this->jwt->createToken($user);
        Notification::fake();

        // Отправляем запрос, на отправку письма для подтверждения почты
        $this
            ->post("/api/v1/users/$user->id/email-verification/send", [], [
                'Authorization' => 'Bearer ' . $accessToken,
            ])
            ->assertUnprocessable()
            ->assertJson([
                'message' => 'Электронная почта уже была подтверждена'
            ]);

        // Проверка, что ничего не было отправлено
        Notification::assertNothingSent();
    }
}