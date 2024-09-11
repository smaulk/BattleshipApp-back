<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $ulid
 * @property string $chain
 * @property int $user_id
 * @property bool $is_blocked
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property DateTimeInterface $created_at
 * @property DateTimeInterface $expired_in
 */
final class RefreshToken extends Model
{
    use HasFactory;
}
