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

    /**
     * @param bool $isUid1 текущий пользователь является uid1
     * @return FriendshipType
     */
    public function toType(bool $isUid1): FriendshipType
    {
        return match ($this) {
            self::REQ_UID1 => $isUid1 ? FriendshipType::OUTGOING : FriendshipType::INCOMING,
            self::REQ_UID2 => $isUid1 ? FriendshipType::INCOMING : FriendshipType::OUTGOING,
            self::FRIEND   => FriendshipType::FRIEND,
        };
    }
}