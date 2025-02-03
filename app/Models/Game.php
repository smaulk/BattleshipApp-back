<?php

namespace App\Models;

use App\Enums\GameStatus;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Parents\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $uid1
 * @property int $uid2
 * @property GameStatus $status
 * @property DateTimeInterface $created_at
 * @property DateTimeInterface $ended_at
 */
class Game extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $table = 'games';

    protected $fillable = [
        'uid1',
        'uid2',
        'status'
    ];

    protected function casts(): array
    {
        return [
            'status'   => GameStatus::class,
            'ended_at' => 'datetime',
        ];
    }

    public static function getNotFoundMessage(): string
    {
        return 'Игра не найдена';
    }
}
