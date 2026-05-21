<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Carbon;

class AdminNotificationAlert extends Component
{
    public string $lastCheckedAt = '';

    public function mount(): void
    {
        $this->lastCheckedAt = now()->toIso8601String();
    }

    public function checkNew(): void
    {
        $user = auth()->user();
        if (! $user) return;

        $newNotifications = $user->unreadNotifications()
            ->where('created_at', '>', Carbon::parse($this->lastCheckedAt))
            ->latest()
            ->get();

        $this->lastCheckedAt = now()->toIso8601String();

        foreach ($newNotifications as $notif) {
            $data = $notif->data;
            $this->dispatch('admin-notif-toast', [
                'title' => $data['title'] ?? 'Nouvelle notification',
                'body'  => $data['body']  ?? '',
                'url'   => $data['actions'][0]['url'] ?? null,
                'id'    => $notif->id,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin-notification-alert');
    }
}
