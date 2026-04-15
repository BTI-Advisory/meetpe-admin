<?php

namespace App\Notifications;

use App\SubSystems\Notifications\AppNotification;
use App\SubSystems\Notifications\Enums\EnumNotificationType;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;
use Symfony\Component\Mime\Email;
use Illuminate\Support\Facades\App;

class RefuseraparticipationForVoyaguerNotification extends Notification
{
    use Queueable;

    public function __construct(private string $name, private string $fcm_tokn)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = (new AppNotification())->AllowedChannelsByUser($notifiable->email, EnumNotificationType::NOTIFICATION_RESERVATION->value);
        if ($this->fcm_tokn == '' || is_null($this->fcm_tokn) || !isset($this->fcm_tokn)) {
            return $channels;
        }

        array_push($channels, FcmChannel::class);
        return $channels;
    }

    public function toFcm($notifiable): FcmMessage
    {
        $notification = ['title' => 'Meet People', 'text' =>  __('general.booking_refused', ['experience' => $this->name]), 'sound' => 'default', 'badge' => '1'];

        return (new FcmMessage(
            notification: new FcmNotification(
                title: 'Meet People',
                body:  __('general.booking_refused', ['experience' => $this->name]),
                //     image: 'https://www.meetpe.fr/azaaz.png'
            ),
        ))
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
                        //    'image' => 'https://www.meetpe.fr/azaaz.png', // Optional image URL for rich notifications
                    ],
                ],
            ]);
    }
    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $now = Carbon::now();
        return (new MailMessage())
            ->from('contact@meetpe.fr', 'MeetPe')
            ->withSymfonyMessage(function (Email $message) {
                $message->getHeaders()->addTextHeader('Custom-Header', 'Header Value');
                $message->sender('contact@meetpe.fr');
            })
            ->subject( __('general.booking_refused', ['experience' => $this->name]))

            ->view('notifications.reservation.'.App::getLocale().'_RefuseraparticipationForVoyaguerNotification', ['username' => $notifiable->name, 'currentYear' => $now->year, "experienceName"=>$this->name]);
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
    public function toVonage(object $notifiable): VonageMessage
    {
        return (new VonageMessage())->content('Your SMS message content');
    }
}
