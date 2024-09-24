<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Classes\Timestamp;
use App\Classes\VerificationManager;
use App\Models\User;
use App\Parents\Test;

final class VerifyEmailTest extends Test
{
    public function testVerifyEmail(): void
    {
        /** @var User $user */
        $user = User::factory()->unverified()->create();
        // Проверяем, что почта не подтверждена
        $this->assertNull($user->email_verified_at);

        $manager = new VerificationManager();
        $id = $user->getKey();
        $hash = $manager->hashString($user->getEmailForVerification());
        $exp = $manager->getNewExp();
        $data = $manager->createData($id, $hash, $exp);
        $signature = $manager->createSign($data);

        // Отправляем запрос на подтверждение почты
        $this->putJson("/api/v1/users/$id/email-verification", [
            'hash'      => $hash,
            'exp'       => $exp,
            'signature' => $signature,
        ])
            ->assertNoContent();

        // Проверяем, что почта подтверждена
        $this->assertNotNull($user->fresh()?->email_verified_at);
    }

    public function testVerifyEmailWithWrongData(): void
    {
        /** @var User $user */
        $user = User::factory()->unverified()->create();
        // Проверяем, что почта не подтверждена
        $this->assertNull($user->email_verified_at);

        // Отправляем запрос на подтверждение почты с истекшими данными
        $this->putJson("/api/v1/users/$user->id/email-verification", [
            'hash'      => 'wrong-hash',
            'exp'       => 1000,
            'signature' => 'wrong-signature',
        ])
            ->assertForbidden()
            ->assertJson([
                'message' => 'Срок действия верификации истек'
            ]);

        // Отправляем запрос на подтверждение почты с некорректными данными
        $this->putJson("/api/v1/users/$user->id/email-verification", [
            'hash'      => 'wrong-hash',
            'exp'       => time() + 1000,
            'signature' => 'wrong-signature',
        ])
            ->assertBadRequest()
            ->assertJson([
                'message' => 'Недействительные данные для верификации'
            ]);

        // Отправляем запрос на подтверждение почты с несуществующим id
        $wrongId = $user->id + 1;
        $this->putJson("/api/v1/users/$wrongId/email-verification", [
            'hash'      => 'wrong-hash',
            'exp'       => time() + 1000,
            'signature' => 'wrong-signature',
        ])
            ->assertNotFound()
            ->assertJson([
                'message' => 'Пользователь не найден'
            ]);
    }
}