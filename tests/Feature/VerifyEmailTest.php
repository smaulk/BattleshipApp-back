<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Parents\Test;

final class VerifyEmailTest extends Test
{
    public function testVerifyEmail(): void
    {
        /** @var User $user */
        $user = User::factory()->unverified()->create();
        $accessToken = $this->jwt->createToken($user);
        // Проверяем, что почта не подтверждена
        $this->assertNull($user->email_verified_at);

        // Отправляем запрос на подтверждение почты
        $this->postJson("/api/v1/users/$user->id/email-verification", [
            'id'   => $user->getKey(),
            'hash' => sha1($user->getEmailForVerification()),
        ], [
            'Authorization' => 'Bearer ' . $accessToken,
        ])
            ->assertNoContent();

        // Проверяем, что почта подтверждена
        $this->assertNotNull($user->fresh()?->email_verified_at);
    }
}