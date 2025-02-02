<?php
declare(strict_types=1);

namespace App\Enums;

enum GameStatus
{
    case CREATED; // Игра создана
    case ABANDONED; // Игра прервана
    case WIN_UID1; // Победил 1 игрок
    case WIN_UID2; // Победил 2 игрок
    case DRAW; // Ничья

    public static function names(): array
    {
        return array_map(fn($status) => $status->name, self::cases());
    }
}