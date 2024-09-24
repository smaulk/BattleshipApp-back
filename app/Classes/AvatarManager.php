<?php
declare(strict_types=1);

namespace App\Classes;

use App\Exceptions\HttpException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;
use Throwable;

final class AvatarManager
{
    public const SIZE = 150;
    public const DISK_NAME = 'userAvatars';

    private Filesystem $disk;

    public function __construct(string $disk = self::DISK_NAME)
    {
        $this->disk = Storage::disk($disk);
    }

    /**
     * Сохраняет изображение на диске, изменяя его размер и кодирует в JPEG
     */
    public function save(UploadedFile $file, int $size = self::SIZE): string
    {
        $manager = new ImageManager(new Driver());

        $filename = $this->getRandomFilename();
        try {
            $path = $this->disk->path($filename);
            $image = $manager->read($file);
            $image
                ->resize($size, $size)
                ->toJpeg(progressive: true)
                ->save($path);
        } catch (Throwable $exception) {
            Log::error($exception);
            throw new HttpException(500, 'Не удалось обработать файл');
        }

        return $filename;
    }

    /**
     * Возвращает случайное название файла
     */
    private function getRandomFilename(): string
    {
        return Str::ulid()->toBase32() . '.jpg';
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
