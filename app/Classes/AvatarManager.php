<?php
declare(strict_types=1);

namespace App\Classes;

use App\Exceptions\HttpException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;
use Throwable;

class AvatarManager
{
    public const SIZE = 150;

    private ImageManager $manager;
    private Filesystem $disk;


    public function __construct(string $disk = 'userAvatars')
    {
        $this->manager = new ImageManager(new Driver());
        $this->disk = Storage::disk($disk);
    }

    public function save(UploadedFile $file, int $size = self::SIZE): string
    {
        $filename = $file->hashName();
        try {
            $path = $this->disk->path($filename);
            $image = $this->manager->read($file);
            $image
                ->resize($size, $size)
                ->save($path);
        } catch (Throwable $exception) {
            Log::error($exception);
            throw new HttpException(500, 'Не удалось обработать файл');
        }

        return $filename;
    }

    public function delete(?string $filename): bool
    {
        return !is_null($filename) && $this->disk->delete($filename);
    }
}
