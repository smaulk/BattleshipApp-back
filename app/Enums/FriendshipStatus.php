<?php
declare(strict_types=1);

namespace App\Enums;

enum FriendshipStatus
{
    case REQ_UID1; // Запрос в друзья от UID1
    case REQ_UID2; // Запрос в друзья от UID2
    case FRIEND; // Друзья

    public static function names(): array
    {
        return array_map(fn($status) => $status->name, self::cases());
    }
}