<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Route;

class Event extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'event_date',
        'event_time',
        'venue',
        'description',
        'invitation_background_path',
        'invitation_qr_position',
        'invitation_qr_size',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'event_time' => 'datetime:H:i',
            'invitation_qr_size' => 'integer',
        ];
    }

    public static function invitationQrPositions(): array
    {
        return [
            'left-top',
            'center-top',
            'right-top',
            'left-middle',
            'center-middle',
            'right-middle',
            'left-bottom',
            'center-bottom',
            'right-bottom',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function guests(): HasMany
    {
        return $this->hasMany(EventGuest::class)->orderBy('first_name')->orderBy('last_name');
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(EventInvitation::class)->latest();
    }

    public function invitationBackgroundUrl(): ?string
    {
        if ($this->invitation_background_path === null) {
            return Route::has('invitations.default-background')
                ? route('invitations.default-background')
                : null;
        }

        return Route::has('events.invitation-background')
            ? route('events.invitation-background', $this)
            : null;
    }

    public function invitationQrPosition(): string
    {
        return in_array($this->invitation_qr_position, self::invitationQrPositions(), true)
            ? $this->invitation_qr_position
            : 'right-bottom';
    }

    public function invitationQrSize(): int
    {
        $size = (int) ($this->invitation_qr_size ?: 180);

        return max(80, min($size, 320));
    }
}
