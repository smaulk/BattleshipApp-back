<?php
declare(strict_types=1);

namespace App\Enums;

enum FriendshipType
{
    case FRIEND; // Друзья
    case OUTGOING; // Исходящий запрос в друзья
    case INCOMING; // Входящий запрос в друзья
}