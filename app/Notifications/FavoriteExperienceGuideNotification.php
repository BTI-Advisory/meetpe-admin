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

class FavoriteExperienceGuideNotification extends Notification
{
    use Queueable;

    public $experienceTitle;
    /**
     * Create a new notification instance.
     */
    public function __construct(private string $fcm_tokn, $experienceTitle)
    {
        $this->experienceTitle = $experienceTitle;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    { 
        $channels = array();
        array_push($channels, FcmChannel::class);
        return $channels;
    }

    public function toFcm($notifiable): FcmMessage
    {
        $notification = array('title' => "Meet People", 'text' => __('general.experience_favoris' ,['experience' => $this->experienceTitle]), 'sound' => 'default', 'badge' => '1');

        return (
            new FcmMessage(
                notification: new FcmNotification(
                    title: "Meet People",
                    body: __('general.experience_favoris', ['experience' => $this->experienceTitle]),
                )
            )
        );
    }

    
}
