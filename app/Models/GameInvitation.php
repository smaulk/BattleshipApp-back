<?php

namespace App\Models;

use App\Parents\Model;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $sender_id
 * @property int $receiver_id
 * @property DateTimeInterface $invited_at
 * @property User $sender
 * @property User $receiver
 * @method static Builder findByUsers(int $senderId, int $receiverId)
 */
final class GameInvitation extends Model
{
    use HasFactory;

    protected $table = 'game_invitations';

    public $incrementing = false;
    public $timestamps = false;

    protected $primaryKey = ['sender_id', 'receiver_id'];

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'invited_at'
    ];

    protected function casts(): array
    {
        return [
            'invited_at' => 'datetime',
        ];
    }

    public static function getNotFoundMessage(): string
    {
        return 'Приглашение в игру не найдено';
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function scopeFindByUsers(Builder $query, int $senderId, int $receiverId): Builder
    {
        return $query
            ->where('sender_id', $senderId)
            ->where('receiver_id', $receiverId);
    }
}
