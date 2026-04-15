<?php

namespace App\Notifications;

use App\SubSystems\Notifications\AppNotification;
use App\SubSystems\Notifications\Enums\EnumNotificationType;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;
use Symfony\Component\Mime\Email;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class RappelExperienceNotificationForGuide extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private string $experienceTitle,
        private string $guideName,
        private string $dateTime,
                string $locale,
        private string $address,
        private array $voyageurs,
        private string $fcm_tokn)
    {
            $this->locale = $locale; // utilise la propriété héritée (publique)
                App::setLocale($this->locale);
                Log::channel('CRON_RAPPEL')->info('[Notification CONSTRUCTOR] Locale définie à : ' . $this->locale);
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
        $notification = ['title' => 'Meet People', 'text' => __('general.rappel_guide_title', ['experience' => $this->experienceTitle]), 'sound' => 'default', 'badge' => '1'];

        return (new FcmMessage(
            notification: new FcmNotification(
                title: 'Meet People',
                body: __('general.rappel_guide_title', ['experience' => $this->experienceTitle]),
                //     image: 'https://www.meetpe.fr/azaaz.png'
            ),
        ))
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
            ->subject(__('general.rappel_guide_title', ['experience' => $this->experienceTitle]))
            ->view('notifications.reservation.'.$this->locale.'_rappelExperienceGuide', [
                'experienceTitle'=>$this->experienceTitle,
                'guideName' => $this->guideName,
                'dateTime'=>$this->dateTime,
                'experienceAddress'=>$this->address,
                'voyageurs'=>$this->voyageurs,
                'currentYear' => $now->year]);
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
