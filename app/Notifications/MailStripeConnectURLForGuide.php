<?php

namespace App\Notifications;

use App\SubSystems\Notifications\AppNotification;
use App\SubSystems\Notifications\Enums\EnumNotificationType;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;
use Symfony\Component\Mime\Email;
use Illuminate\Support\Facades\App;

class MailStripeConnectURLForGuide extends Notification
{
    use Queueable;
    private $stripe_connect_url;

    /**
     * Create a new notification instance.
     */
    public function __construct(private string $fcm_tokn, $stripe_connect_url)
    {
        $this->stripe_connect_url = $stripe_connect_url;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = (new AppNotification())->AllowedChannelsByUser($notifiable->email, EnumNotificationType::NOTIFICATION_MEETPE->value);
        return $channels;
    }
   
    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $now = Carbon::now();
        return (new MailMessage)
            ->from("contact@meetpe.fr", "MeetPe")
            ->withSymfonyMessage(function (Email $message) {
                $message->getHeaders()->addTextHeader(
                    'Custom-Header',
                    'Header Value'
                );
                $message->sender("contact@meetpe.fr");
            })
            ->subject(__('general.mail_stripe_title'))

            ->view(
                'notifications.profile.'.App::getLocale().'_stripeConnectGuide',
                ["username" => $notifiable->name, "currentYear" => $now->year, "url"=>$this->stripe_connect_url]
            );
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
