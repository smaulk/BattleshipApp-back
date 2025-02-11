<?php
declare(strict_types=1);

namespace App\Enums;

enum FriendshipType: int
{
    case OUTGOING = 1; // Исходящий запрос в друзья
    case INCOMING = 2; // Входящий запрос в друзья
    case FRIEND   = 3; // Друзья

    /**
     * Возвращает пару FriendshipStatus
     * @return array{FriendshipStatus, FriendshipStatus}
     */
    public function toStatuses(): array
    {
        return match ($this) {
            FriendshipType::FRIEND   => [FriendshipStatus::FRIEND, FriendshipStatus::FRIEND],
            FriendshipType::OUTGOING => [FriendshipStatus::REQ_UID1, FriendshipStatus::REQ_UID2],
            FriendshipType::INCOMING => [FriendshipStatus::REQ_UID2, FriendshipStatus::REQ_UID1],
        };
    }
}