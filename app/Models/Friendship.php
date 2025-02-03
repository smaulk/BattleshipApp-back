<?php

namespace App\Models;

use App\Enums\FriendshipStatus;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Parents\Model;

/**
 * @property int $id
 * @property int $uid1
 * @property int $uid2
 * @property FriendshipStatus $status
 * @property DateTimeInterface $created_at
 * @property DateTimeInterface $updated_at
 * @method static Builder findByUsers(int $uid1, int $uid2)
 */
final class Friendship extends Model
{
    use HasFactory;

    protected $table = 'friendships';

    public $timestamps = true;

    protected $fillable = [
        'uid1',
        'uid2',
        'status'
    ];

    protected function casts(): array
    {
        return [
            'status' => FriendshipStatus::class,
        ];
    }

    public static function getNotFoundMessage(): string
    {
        return 'Не существует такой заявки или дружбы';
    }

    public function scopeFindByUsers(Builder $query, int $uid1, int $uid2): Builder
    {
        [$minId, $maxId] = sort_nums($uid1, $uid2);

        return $query
            ->where('uid1', $minId)
            ->where('uid2', $maxId);
    }
}
