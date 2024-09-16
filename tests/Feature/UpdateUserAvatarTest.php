<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UpdateUserAvatarTest extends TestCase
{
    public function testUpdateUserAvatar()
    {
        Storage::fake('userAvatars');
        $disk = Storage::disk('userAvatars');

        /** @var User $user */
        $user = User::factory()->create();
        $accessToken = $this->jwt->createToken($user);

        // Обновяем аватар
        $this
            ->putJson("/api/v1/users/$user->id/avatar", [
                'avatar' => $avatarFile1 = UploadedFile::fake()->image('avatar1.jpg'),
            ], [
                'Authorization' => "Bearer $accessToken",
            ])
            ->assertNoContent();

        // Проверяем наличие файла
        $disk->assertExists($avatarFile1->hashName());

        // Обновляем аватар второй раз другим файлом
        $this
            ->putJson("/api/v1/users/$user->id/avatar", [
                'avatar' => $avatarFile2 = UploadedFile::fake()->image('avatar2.jpg'),
            ], [
                'Authorization' => "Bearer $accessToken",
            ])
            ->assertNoContent();

        // Проверяем, что новый файл существует, а старый нет
        $disk->assertMissing($avatarFile1->hashName());
        $disk->assertExists($avatarFile2->hashName());
    }
}
