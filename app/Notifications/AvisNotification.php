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
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\App;

class AvisNotification extends Notification
{
    use Queueable;

    public function __construct(
        private  $experience, 
        private  $voyageur,
        private $guide)
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
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        App::setLocale($this->voyageur->device_language);
        $now = Carbon::now();
        return (new MailMessage())
            ->from('contact@meetpe.fr', 'MeetPe')
            ->withSymfonyMessage(function (Email $message) {
                $message->getHeaders()->addTextHeader('Custom-Header', 'Header Value');
                $message->sender('contact@meetpe.fr');
            })
            ->subject(__('avis.send_love'))
            ->view('notifications.experience.'.$this->voyageur->device_language.'_avisvoyageur', [
                'username' => $notifiable->name,
                'encryptedId'=>Crypt::encryptString($this->experience->id.'-'.$this->voyageur->id.'-'.$this->voyageur->device_language),
                'experienceName'=>$this->experience->getTitleForLocale($this->voyageur->device_language),
                'guideName'=>$this->guide->name,
                'voyageurName'=> $this->voyageur->name,
                'photo'=> $this->experience->photoprincipal->photo_url,
                'currentYear' => $now->year]);
    }

   
}
