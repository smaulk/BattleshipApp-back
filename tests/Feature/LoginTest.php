<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Support\Str;
use Tests\TestCase;

final class LoginTest extends TestCase
{
    public function testLogin(): void
    {
        // Создаем пользователя
        User::factory()->create([
            'nickname' => Str::random(),
            'email'    => $email = Str::random() . '@example.com',
            'password' => $password = Str::password(),
        ]);

        // Делаем вход с некорректными данными
        $this
            ->postJson('api/v1/login', [
                'email'    => $email,
                'password' => $password . 'error',
            ])
            ->assertUnauthorized()
            ->assertJson([
                'message' => 'Некорректные данные для входа'
            ]);

        // Делаем вход с корректными данными
        $this
            ->postJson('api/v1/login', [
                'email'    => $email,
                'password' => $password,
            ])
            ->assertOk()
            ->assertJsonStructure([
                'accessToken',
                'refreshToken'
            ]);

        $this->assertDatabaseCount(RefreshToken::class, 1);
    }
}
