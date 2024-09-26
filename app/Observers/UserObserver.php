<?php

namespace App\Observers;

use App\Models\User;
use App\Parents\Observer;

final class UserObserver extends Observer
{
    public function created(User $user): void
    {
        // Отправка письма для подтверждения почты
        if (!$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
        }
    }

    public function updating(User $user): void
    {
        // Если изменился email, убираем время подтверждения почты
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
    }

    public function updated(User $user): void
    {
        // Если изменился email, отправляем новое письмо для подтверждения
        if ($user->isDirty('email')) {
            $user->sendEmailVerificationNotification();
        }
    }

    public function deleted(User $user): void
    {
        //
    }

    public function restored(User $user): void
    {
        //
    }

    public function forceDeleted(User $user): void
    {
        //
    }
}
