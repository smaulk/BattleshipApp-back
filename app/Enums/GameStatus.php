<?php
declare(strict_types=1);

namespace App\Enums;

enum GameStatus: int
{
    case CREATED   = 1; // Игра создана
    case ABANDONED = 2; // Игра прервана
    case WIN_UID1  = 3; // Победил 1 игрок
    case WIN_UID2  = 4; // Победил 2 игрок
    case DRAW      = 5; // Ничья

    public static function names(): array
    {
        return array_map(fn($status) => $status->name, self::cases());
    }

    public static function fromName(string $name): self
    {
        return constant("self::$name");
    }

    /**
     * @param bool $isUid1 текущий пользователь является uid1
     * @return GameType
     */
    public function toType(bool $isUid1): GameType
    {
        return match ($this) {
            self::CREATED => GameType::CREATED,
            self::ABANDONED => GameType::ABANDONED,
            self::WIN_UID1 => $isUid1 ? GameType::WIN : GameType::LOSE,
            self::WIN_UID2 => $isUid1 ? GameType::LOSE : GameType::WIN,
            self::DRAW => GameType::DRAW,
        };
    }
}