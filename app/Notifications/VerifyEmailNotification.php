<?php

namespace App\Notifications;

use App\Classes\Timestamp;
use App\Classes\VerificationManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

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
        $manager = new VerificationManager();
        $id = $notifiable->getKey();
        $hash = $manager->hashString($notifiable->getEmailForVerification());
        $exp = $manager->getNewExp();
        $data = $manager->createData($id, $hash, $exp);
        $signature = $manager->createSign($data);

        $query = http_build_query([
            'expires'   => $exp,
            'signature' => $signature,
        ]);

        return config('app.frontend_url')
            . self::ROUTE
            . "/$id/$hash?$query";
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
