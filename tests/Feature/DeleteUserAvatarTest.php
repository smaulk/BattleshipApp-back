<?php

namespace Tests\Feature;

use App\Models\User;
use App\Parents\Test;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

final class DeleteUserAvatarTest extends Test
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
                'avatar' => UploadedFile::fake()->image('avatar1.jpg'),
            ], [
                'Authorization' => "Bearer $accessToken",
            ])
            ->assertNoContent();

        // Проверяем наличие файла и его запись в БД
        $this->assertNotNull($filename = $user->fresh()?->avatar_filename);
        $disk->assertExists($filename);

        // Проверяем удаление аватара
        $this
            ->deleteJson("/api/v1/users/$user->id/avatar", [], [
                'Authorization' => "Bearer $accessToken",
            ])
            ->assertNoContent();

        $disk->assertMissing($filename);
        $this->assertNull($user->fresh()?->avatar_filename);
    }
}
