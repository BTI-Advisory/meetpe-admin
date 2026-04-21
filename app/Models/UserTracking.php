<?php

namespace App\Models;

use App\Enums\TrackingAction;
use Illuminate\Database\Eloquent\Model;

class UserTracking extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'route',
        'actor_type',
        'subject_type',
        'action',
        'method',
        'metadata',
        'timestamp',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    protected $appends = [
        'action_label',
        'action_color',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    private function trackingEnum(): ?TrackingAction
    {
        return TrackingAction::tryFrom($this->action);
    }

    public function getActionLabelAttribute(): string
    {
        return $this->trackingEnum()?->label() ?? $this->action;
    }

    public function getActionColorAttribute(): string
    {
        return $this->trackingEnum()?->color() ?? 'secondary';
    }
}
