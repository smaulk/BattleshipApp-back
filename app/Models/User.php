<?php
declare(strict_types=1);

namespace App\Models;

use App\Classes\AvatarManager;
use App\Enums\FriendshipStatus;
use App\Enums\FriendshipType;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use App\Parents\Model;
use DateTimeInterface;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

/**
 * @property int $id
 * @property string $nickname
 * @property string $email
 * @property string $email_verified_at
 * @property string|null $avatar_filename
 * @property string $password
 * @property DateTimeInterface $created_at
 * @property DateTimeInterface $updated_at
 */
final class User extends Model implements
    AuthenticatableContract,
    CanResetPasswordContract,
    MustVerifyEmailContract
{
    use Authenticatable, Notifiable, HasFactory, CanResetPassword, MustVerifyEmail;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nickname',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    protected $appends = [
        'avatar_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    protected function avatarUrl(): Attribute
    {
        $getter = function () {
            return $this->avatar_filename
                ? (new AvatarManager())->getUrl($this->avatar_filename)
                : null;
        };

        return new Attribute(get: $getter);
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification());
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public static function getNotFoundMessage(): string
    {
        return 'Пользователь не найден';
    }

    /**
     * Дружеские отношение авторизованного пользователя по отношению к данному
     * @param int $userId авторизованный пользователь
     */
    public function friendshipType(int $userId): FriendshipType|null
    {
        [$minId, $maxId] = sort_nums($this->id, $userId);
        $friendship = DB::table('friendships')
            ->select('status')
            ->where('uid1', $minId)
            ->where('uid2', $maxId)
            ->first();

        if (is_null($friendship)) {
            return null;
        }

        $status = $friendship->status;
        $isRequester = $minId === $userId; // Флаг, указывающий, кто является инициатором

        return match ($status) {
            FriendshipStatus::REQ_UID1->name => $isRequester ? FriendshipType::OUTGOING : FriendshipType::INCOMING,
            FriendshipStatus::REQ_UID2->name => $isRequester ? FriendshipType::INCOMING : FriendshipType::OUTGOING,
            FriendshipStatus::FRIEND->name   => FriendshipType::FRIEND,
            default                          => null,
        };
    }

}