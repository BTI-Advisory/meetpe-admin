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

    protected $casts = [];

    public function getMetadataAttribute($value): ?array
    {
        if (empty($value)) return null;
        $base64   = json_decode($value) ?? $value;
        $decoded  = base64_decode($base64);
        if (empty($decoded)) return null;
        $inflated = @zlib_decode($decoded);
        if ($inflated === false) return null;
        return json_decode($inflated, true);
    }

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
