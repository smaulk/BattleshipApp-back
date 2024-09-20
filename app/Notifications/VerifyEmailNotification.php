<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private const ROUTE = '/verify-email';

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return $this->buildMailMessage(
            $this->verificationUrl($notifiable)
        );
    }

    protected function verificationUrl($notifiable): string
    {
        $id = $notifiable->getKey();
        $hash = sha1($notifiable->getEmailForVerification());
        $query = http_build_query([
            'id' => $id,
            'hash' => $hash,
        ]);

        return env('APP_FRONT_URL')
            . self::ROUTE . '?'
            . $query;
    }

    protected function buildMailMessage($url): MailMessage
    {
        return (new MailMessage)
            ->subject('Подтверждение электронной почты')
            ->line('Нажмите кнопку ниже, чтобы подтвердить свой адрес электронной почты.')
            ->action('Подтвердить почту', $url)
            ->line('Если вы не создавали учетную запись проигнорируйте данное письмо.');
    }
}
