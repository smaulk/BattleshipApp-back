<?php
declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{

    public string $token;
    private const ROUTE = '/reset-password';

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return $this->buildMailMessage(
            $this->resetUrl($notifiable)
        );
    }

    protected function resetUrl($notifiable): string
    {
        $query = http_build_query([
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        return config('app.frontend_url')
            . self::ROUTE
            . "/$this->token?$query";
    }

    protected function buildMailMessage(string $url): MailMessage
    {
        return (new MailMessage)
            ->subject('Сброс пароля')
            ->line('Вы получили это письмо, потому что мы получили запрос на сброс пароля для вашей учетной записи.')
            ->action('Сбросить пароль', $url);
    }
}