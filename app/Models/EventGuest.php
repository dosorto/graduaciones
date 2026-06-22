<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Services\InvitationSyncService;

class EventGuest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'event_id',
        'first_name',
        'last_name',
        'phone',
        'invitation_count',
    ];

    protected $appends = [
        'full_name',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(EventInvitation::class)->orderBy('sequence_number');
    }

    protected function fullName(): Attribute
    {
        return Attribute::get(fn () => trim("{$this->first_name} {$this->last_name}"));
    }

    protected static function booted(): void
    {
        static::created(function (EventGuest $guest): void {
            app(InvitationSyncService::class)->syncForGuest($guest);
        });

        static::updated(function (EventGuest $guest): void {
            if ($guest->wasChanged('invitation_count')) {
                app(InvitationSyncService::class)->syncForGuest($guest);
            }
        });
    }
}
