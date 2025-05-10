<?php

namespace App\Models;

use App\Enums\GameType;
use App\Parents\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $user_id
 * @property int $games
 * @property int $wins
 * @property int $losses
 * @property int $points
 * @property User $user
 */
final class UserStatistic extends Model
{
    use HasFactory, SoftDeletes;

    public const POINT_VALUES = [
        GameType::WIN->value  => 10,
        GameType::LOSE->value => -5,
    ];

    public const LEADERBOARD_COUNT = 20;

    protected $primaryKey = 'user_id';
    public $incrementing = false;
    public $timestamps = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'games',
        'wins',
        'losses',
        'points',
    ];

    public static function getNotFoundMessage(): string
    {
        return 'Статистика не найдена';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function getPointValue(GameType $type): int
    {
        return self::POINT_VALUES[$type->value] ?? 0;
    }
}
