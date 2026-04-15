<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;
use App\SubSystems\Notifications\AppNotification;
use App\SubSystems\Notifications\Enums\EnumNotificationType;

class NewChatMessageNotification extends Notification
{
    use Queueable;

    public $message;
    public $recipient;
    public $unreadCount; 
    public $role;        
    public function __construct($message,  $recipient, $unreadCount, $role)
    {
        $this->message = $message;
        $this->recipient = $recipient;
        $this->unreadCount = $unreadCount;
        $this->role = $role;

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
        array_push($channels,"database");
        return $channels;
    }

    public function toArray($notifiable)
    {
        return [
            'title' => $this->message->sender->name,
            'body' => $this->message->message,
            'channel_id' => $this->message->channel_id,
        ];
    }

    public function toFcm($notifiable): FcmMessage
    {
        return (new FcmMessage(
            notification: new FcmNotification(
                title: $this->message->sender->name,
                body: $this->message->message,
            )
        ))
        ->data(
            [
            'type'        => 'chat_message',
            'channel_id'  => (string) $this->message->channel_id,
            'user_role'   => $this->role
            ]
        )
        ->custom([
                'android' => [
                    'priority' => 'HIGH',   // important
                    'notification' => [
                        'channel_id' =>  (string) $this->message->channel_id,
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                        'notification_count' => $this->unreadCount, // 👈 ton calcul
                    ]
                ],
                // 🍏 iOS (APNs)
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'alert' => [
                                'title' => $this->message->sender->name,
                                'body'  =>  $this->message->message,
                            ],
                            'badge' => $this->unreadCount, // total NON lus
                            'sound' => 'default',
                        ]
                    ]
                ]
        ]);
    
    }
   
}
