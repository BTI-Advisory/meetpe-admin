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

class MakeExperienceNonComplete extends Notification
{
    use Queueable;

    protected array $reason;

    public function __construct(string $reason, private string $experienceName, private string $fcm_tokn)
    {
        $this->reason = $this->formatMessage($reason);
    }

    private function formatMessage(string $reason_p): array
    {
        if (strstr($reason_p, '_')) {
            $reasons_arr = [];
            foreach (explode('_', $reason_p) as $r) {
                array_push($reasons_arr, $r);
            }
            return $reasons_arr;
        }
        return [$reason_p];
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
            ->subject(__('general.experience_comment'))
            ->view(
                'notifications.experience.' . App::getLocale() . '_your_experience_is_non_complete',
                ['username' => $notifiable->name, 'currentYear' => $now->year, 'reason' => $this->reason, 'experienceName' => $this->experienceName]
            );
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
