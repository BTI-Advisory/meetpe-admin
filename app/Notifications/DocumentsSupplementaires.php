<?php

namespace App\Notifications;

use App\SubSystems\Notifications\AppNotification;
use App\SubSystems\Notifications\Enums\EnumNotificationType;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Symfony\Component\Mime\Email;
use Illuminate\Support\Facades\App;

class DocumentsSupplementaires extends Notification
{
    use Queueable;

    public function __construct(private string $fcm_tokn, private string $experienceName)
    {
    }

    public function via(object $notifiable): array
    {
        return (new AppNotification())->AllowedChannelsByUser($notifiable->email, EnumNotificationType::NOTIFICATION_RESERVATION->value);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $now = Carbon::now();
        return (new MailMessage)
            ->from("contact@meetpe.fr", "MeetPe")
            ->withSymfonyMessage(function (Email $message) {
                $message->getHeaders()->addTextHeader('Custom-Header', 'Header Value');
                $message->sender("contact@meetpe.fr");
            })
            ->subject(__('general.experience_supp_doc'))
            ->view(
                'notifications.welcome.' . App::getLocale() . '_documentsupplementaires',
                ['username' => $notifiable->name, 'currentYear' => $now->year, 'experienceTitle' => $this->experienceName]
            );
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
