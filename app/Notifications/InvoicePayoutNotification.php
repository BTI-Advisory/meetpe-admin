<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Symfony\Component\Mime\Email;

class InvoicePayoutNotification extends Notification
{
    use Queueable;

    private $guide;
    private $filePath;
    private $fileName;
    private $periode;

    public function __construct($guide, $filePath, $fileName, $title ,$periode)
    {
        $this->guide    = $guide;
        $this->filePath = $filePath;   // chemin local OU URL S3
        $this->fileName = $fileName;
        $this->periode  = $periode;
        $this->title    = $title;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {

        return (new MailMessage)
        ->from('contact@meetpe.fr', 'MeetPe')
        ->subject("{$this->title}")
        ->view('notifications.payout.'.$this->guide->device_language.'_facture', [ // 👈 ton Blade personnalisé
            'guide'   => $this->guide,
            'periode' => $this->periode,
        ])
        ->withSymfonyMessage(function (Email $message) {
            if (file_exists($this->filePath)) {
                $message->attachFromPath($this->filePath, $this->fileName, 'application/pdf');
            }
        });
    }
}

