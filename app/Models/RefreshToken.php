<?php
declare(strict_types=1);

namespace App\Models;

use App\Exceptions\HttpException;
use App\Parents\Model;
use DateTimeInterface;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * @property string $ulid
 * @property string $chain
 * @property int $user_id
 * @property bool $is_blocked
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property DateTimeInterface $created_at
 * @property DateTimeInterface $expires_at
 */
final class RefreshToken extends Model
{
    protected $primaryKey = 'ulid';

    public $incrementing = false;

    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'is_blocked' => 'boolean',
            'expires_at' => 'datetime',
        ];
    }

    /**
     * Блокирировка токена
     */
    public function block(): void
    {
        try {
            $this->is_blocked = true;
            $this->saveOrFail();
        } catch (Throwable $exception) {
            Log::error($exception);
            throw new HttpException(500);
        }
    }
}
