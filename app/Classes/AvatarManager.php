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

    private Filesystem $disk;

    public function __construct(string $disk = 'userAvatars')
    {
        $this->disk = Storage::disk($disk);
    }

    /**
     * Сохраняет изображение на диске, изменяя его размер
     */
    public function save(UploadedFile $file, int $size = self::SIZE): string
    {
        $manager = new ImageManager(new Driver());

        $filename = $file->hashName();
        try {
            $path = $this->disk->path($filename);
            $image = $manager->read($file);
            $image
                ->resize($size, $size)
                ->save($path);
        } catch (Throwable $exception) {
            Log::error($exception);
            throw new HttpException(500, 'Не удалось обработать файл');
        }

        return $filename;
    }

    /**
     * Удаляет файл с диска
     */
    public function delete(?string $filename): bool
    {
        return !is_null($filename) && $this->disk->delete($filename);
    }

    /**
     * Возвращает URL к файлу.
     * Если файла не существует, возвращает NULL
     */
    public function getUrl(string $filename): ?string
    {
        return $this->disk->exists($filename)
            ? $this->disk->url($filename)
            : null;
    }
}
