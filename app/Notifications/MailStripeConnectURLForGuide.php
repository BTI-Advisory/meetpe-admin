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

class MailStripeConnectURLForGuide extends Notification
{
    use Queueable;

    public function __construct(private string $fcm_tokn, private $stripe_connect_url)
    {
    }

    public function via(object $notifiable): array
    {
        return (new AppNotification())->AllowedChannelsByUser($notifiable->email, EnumNotificationType::NOTIFICATION_MEETPE->value);
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
            ->subject(__('general.mail_stripe_title'))
            ->view(
                'notifications.profile.' . App::getLocale() . '_stripeConnectGuide',
                ['username' => $notifiable->name, 'currentYear' => $now->year, 'url' => $this->stripe_connect_url]
            );
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
