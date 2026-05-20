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

class YourExperienceIsValid extends Notification
{
    use Queueable;

    public function __construct(private string $fcm_tokn, private string $experienceName)
    {
    }

    public function via(object $notifiable): array
    {
        $channels = (new AppNotification())->AllowedChannelsByUser($notifiable->email, EnumNotificationType::NOTIFICATION_RESERVATION->value);
        if (!empty($this->fcm_tokn)) {
            $channels[] = FcmChannel::class;
        }
        return $channels;
    }

    public function toFcm($notifiable): FcmMessage
    {
        return (new FcmMessage(
            notification: new FcmNotification(
                title: 'Meet People',
                body: __('general.experience_validated', ['experience' => $this->experienceName]),
            ),
        ))->custom([
            'android' => ['notification' => ['color' => '#FF4C00']],
            'apns'    => ['notification' => ['sound' => 'default', 'badge' => '1']],
        ]);
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
            ->subject(__('general.experience_validated', ['experience' => $this->experienceName]))
            ->view(
                'notifications.experience.' . App::getLocale() . '_experience_validated',
                ['username' => $notifiable->name, 'currentYear' => $now->year, 'experienceName' => $this->experienceName]
            );
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
