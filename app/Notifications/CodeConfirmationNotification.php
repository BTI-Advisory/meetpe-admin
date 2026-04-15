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

class CodeConfirmationNotification extends Notification
{
    use Queueable;
    private $codeConfirmation;
    private string $username;

    /**
     * Create a new notification instance.
     */
    public function __construct($codeConfirmation, $username)
    {
        //
        $this->codeConfirmation = $codeConfirmation;
        $this->username = $username;
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
    /*   public function toFcm($notifiable): FcmMessage
    {
    $notification = array('title' => "Code de sécurité 🔐", 'text' => "Code de sécurité 🔐", 'sound' => 'default', 'badge' => '1');

    return (
    new FcmMessage(
    notification: new FcmNotification(
    title: "Code de sécurité 🔐",
    body: "Code de sécurité 🔐",
    image: 'https://www.meetpe.fr/azaaz.png'
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
    'image' => 'https://www.meetpe.fr/azaaz.png', // Optional image URL for rich notifications
    ],
    ],
    ]);
    } */

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $now = Carbon::now();

        $int_str = strval($this->codeConfirmation); // Convert to string

        return (new MailMessage)
            ->from("no-reply@meetpe.fr", "MeetPe")
            ->withSymfonyMessage(function (Email $message) {
                $message->getHeaders()->addTextHeader(
                    'Custom-Header',
                    'Header Value'
                );
                $message->sender("no-reply@meetpe.fr");
            })
            ->subject(__('general.security_code'))
            ->view(
                'notifications.welcome.'.App::getLocale().'_code_confirmation',
                ["username" => explode("@", $notifiable->email)[0], "currentYear" => $now->year, "code_arr" => $int_str]

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
