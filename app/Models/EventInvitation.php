<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'event_guest_id',
        'sequence_number',
        'code',
        'public_token',
        'sent_at',
        'used_at',
        'validated_by_user_id',
        'reentry_enabled_at',
        'reentry_enabled_by_user_id',
        'reentry_used_at',
        'reentry_validated_by_user_id',
        'reentry_count',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'used_at' => 'datetime',
            'reentry_enabled_at' => 'datetime',
            'reentry_used_at' => 'datetime',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(EventGuest::class, 'event_guest_id');
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by_user_id');
    }

    public function reentryEnabledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reentry_enabled_by_user_id');
    }

    public function reentryValidator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reentry_validated_by_user_id');
    }

    public function isUsed(): bool
    {
        return $this->used_at !== null;
    }

    public function hasPendingReentry(): bool
    {
        return $this->used_at !== null
            && $this->reentry_enabled_at !== null
            && $this->reentry_used_at === null;
    }

    public function hasRegisteredReentry(): bool
    {
        return $this->reentry_count > 0;
    }

    public function publicUrl(): string
    {
        return route('invitations.public.show', $this->public_token);
    }

    public function imageUrl(): string
    {
        return route('invitations.public.image', $this->public_token);
    }
}
