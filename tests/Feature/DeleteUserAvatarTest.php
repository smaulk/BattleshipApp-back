<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DeleteUserAvatarTest extends TestCase
{
    public function testDeleteUserAvatar()
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

        // Проверяем удаление аватара
        $this
            ->deleteJson("/api/v1/users/$user->id/avatar", [], [
                'Authorization' => "Bearer $accessToken",
            ])
            ->assertNoContent();

        $disk->assertMissing($avatarFile1->hashName());
    }
}
