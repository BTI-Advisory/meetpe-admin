<?php

namespace App\Notifications;

use App\SubSystems\Notifications\AppNotification;
use App\SubSystems\Notifications\Enums\EnumNotificationType;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class AccountActivated extends Notification
{
    use Queueable;
    private string $title;
    private string $desc;
    public function __construct(string $title, string $desc)
    {

        $this->title = $title;
        $this->desc = $desc;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    /*   public function via(object $notifiable): array
    {
    return ['mail'];
    } */
    public function via($notifiable)
    {
        $channels = (new AppNotification())->AllowedChannelsByUser($notifiable->email, EnumNotificationType::NOTIFICATION_MEETPE->value);
        array_push($channels, FcmChannel::class);
        return $channels;

    }
    public function toFcm($notifiable): FcmMessage
    {
        $notification = array('title' => $this->title, 'text' => $this->desc, 'sound' => 'default', 'badge' => '1');

        return (
            new FcmMessage(
                notification: new FcmNotification(
                    title: $this->title,
                    body: $this->desc,
                    // image: 'https://www.meetpe.fr/azaaz.png'
                )
            )
        )
            ->data(['data1' => 'value', 'data2' => 'value2'])
            ->custom([
                'android' => [
                    'notification' => [
                        'color' => '#0A0A0A',
                    ],
                    'fcm_options' => [
                        'analytics_label' => 'analytics',
                    ],
                ],
                'apns' => [
                    'notification' => $notification,
                    'fcm_options' => [
                        'analytics_label' => 'analytics',
                        //         'image' => 'https://www.meetpe.fr/azaaz.png', // Optional image URL for rich notifications
                    ],
                ],
            ]);
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
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
