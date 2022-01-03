<?php

namespace App\Integrations\Strava\Models;

use App\Integrations\Strava\StravaToken;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Linkeys\UrlSigner\Models\Link;
use Linkeys\UrlSigner\Support\LinkRepository\LinkRepository;

class StravaClient extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'client_secret'
    ];

    protected $hidden = [
        'client_id', 'client_secret'
    ];

    protected $appends = [
        'is_connected',
        'invitation_link',
        'invitation_link_expired',
        'invitation_link_expires_at'
    ];

    protected $casts = [
        'client_id' => 'encrypted',
        'client_secret' => 'encrypted',
        'webhook_verify_token' => 'encrypted',
        'used_15_min_calls' => 'integer',
        'used_daily_min_calls' => 'integer',
        'pending_calls' => 'integer',
        'invitation_link_expires_at' => 'datetime',
        '15_mins_resets_at' => 'datetime',
        'daily_resets_at' => 'datetime',
        'enabled' => 'boolean'
    ];

    protected static function booted()
    {
        static::creating(function (StravaClient $client) {
            if ($client->user_id === null) {
                $client->user_id = Auth::id();
            }
            if($client->webhook_verify_token === null) {
                $client->webhook_verify_token = Str::random(20);
            }
        });
    }

    public function getInvitationLinkAttribute(): ?string
    {
        return $this->getInvitationLink()?->getFullUrl();
    }

    public function getInvitationLinkExpiresAtAttribute(): ?\DateTimeInterface
    {
        return $this->getInvitationLink()?->expiry;
    }

    public function getInvitationLinkExpiredAttribute(): ?bool
    {
        return $this->getInvitationLink()?->expired();
    }

    public function getInvitationLink(): ?Link
    {
        if($this->invitation_link_uuid !== null) {
            return app(LinkRepository::class)->findByUuid($this->invitation_link_uuid);
        }
        return null;
    }

    public function getIsConnectedAttribute(): bool
    {
        if(Auth::check()) {
            return $this->tokens()->forUser(Auth::id())->count() > 0;
        }
        return false;
    }

    public function tokens()
    {
        return $this->hasMany(StravaToken::class);
    }

    public static function scopeEnabled(Builder $query)
    {
        $query->where('enabled', true);
    }

    public function redirectUrl(): string
    {
        return route('strava.callback', ['client' => $this->id]);
    }

    public static function scopeForUser(Builder $query, int $userId)
    {
        $query->where('user_id', $userId)
            ->orWhereHas('sharedUsers', function(Builder $query) use ($userId) {
                $query->where('id', $userId);
            });
            // or where system
    }

    public function sharedUsers()
    {
        return $this->belongsToMany(User::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
