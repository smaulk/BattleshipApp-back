<?php
declare(strict_types=1);

namespace App\Enums;

enum FriendshipType
{
    case FRIEND; // Друзья
    case OUTGOING; // Исходящий запрос в друзья
    case INCOMING; // Входящий запрос в друзья

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