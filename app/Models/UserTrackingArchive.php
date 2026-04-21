<?php

namespace App\Models;

use App\Enums\TrackingAction;
use Illuminate\Database\Eloquent\Model;

class UserTrackingArchive extends Model
{
    protected $table = 'user_trackings_archive';

    protected $fillable = [
        'user_id',
        'actor_type',
        'subject_type',
        'action',
        'method',
        'route',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getActionLabelAttribute(): string
    {
        return TrackingAction::tryFrom($this->action)?->label() ?? $this->action;
    }

    public function getActionColorAttribute(): string
    {
        return TrackingAction::tryFrom($this->action)?->color() ?? 'secondary';
    }
}
