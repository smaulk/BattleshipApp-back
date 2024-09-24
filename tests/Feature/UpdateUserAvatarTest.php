<?php

namespace Tests\Feature;

use App\Models\User;
use App\Parents\Test;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

final class UpdateUserAvatarTest extends Test
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
                'avatar' => UploadedFile::fake()->image('avatar1.jpg'),
            ], [
                'Authorization' => "Bearer $accessToken",
            ])
            ->assertNoContent();

        // Проверяем наличие файла и его запись в БД
        $this->assertNotNull($filename1 = $user->fresh()?->avatar_filename);
        $disk->assertExists($filename1);

        // Обновляем аватар второй раз другим файлом
        $this
            ->putJson("/api/v1/users/$user->id/avatar", [
                'avatar' => UploadedFile::fake()->image('avatar2.jpg'),
            ], [
                'Authorization' => "Bearer $accessToken",
            ])
            ->assertNoContent();

        $filename2 = $user->fresh()?->avatar_filename;
        $this->assertNotEquals($filename1, $filename2);
        // Проверяем, что новый файл существует, а старый нет
        $disk->assertMissing($filename1);
        $disk->assertExists($filename2);
    }
}
