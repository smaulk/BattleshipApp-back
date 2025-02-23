<?php
declare(strict_types=1);

namespace App\Enums;

enum GameType: int
{
    case CREATED   = 1; // Игра создана
    case ABANDONED = 2; // Игра прервана
    case WIN       = 3; // Победа
    case LOSE      = 4; // Поражение
    case DRAW      = 5; // Ничья

    /**
     * Возвращает пару GameStatus
     * @return array{GameStatus, GameStatus}
     */
    public function toStatuses(): array
    {
        return match ($this) {
            self::CREATED   => [GameStatus::CREATED, GameStatus::CREATED],
            self::ABANDONED => [GameStatus::ABANDONED, GameStatus::ABANDONED],
            self::WIN       => [GameStatus::WIN_UID1, GameStatus::WIN_UID2],
            self::LOSE      => [GameStatus::WIN_UID2, GameStatus::WIN_UID1],
            self::DRAW      => [GameStatus::DRAW, GameStatus::DRAW],
        };
    }

    public function toStatus(bool $isUid1): GameStatus
    {
        return match ($this) {
            self::CREATED   => GameStatus::CREATED,
            self::ABANDONED => GameStatus::ABANDONED,
            self::WIN       => $isUid1 ? GameStatus::WIN_UID1 : GameStatus::WIN_UID2,
            self::LOSE      => $isUid1 ? GameStatus::WIN_UID2 : GameStatus::WIN_UID1,
            self::DRAW      => GameStatus::DRAW,
        };
    }
}
