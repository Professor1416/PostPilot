<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class ConnectedAccount extends Model
{
    protected $fillable = [
        'user_id', 'platform', 'platform_user_id',
        'account_name', 'account_type', 'profile_picture_url',
        'access_token', 'token_expires_at', 'is_active', 'last_used_at',
    ];

    protected $casts = [
        'is_active'        => 'boolean',
        'token_expires_at' => 'datetime',
        'last_used_at'     => 'datetime',
    ];

    protected $hidden = ['access_token'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ─── Token encryption ─────────────────────────────────────────────────────

    public function setAccessTokenAttribute(string $value): void
    {
        // Encrypt only if not already encrypted
        if (!str_starts_with($value, 'eyJ')) {
            $this->attributes['access_token'] = Crypt::encryptString($value);
        } else {
            $this->attributes['access_token'] = $value;
        }
    }

    public function getDecryptedToken(): string
    {
        return Crypt::decryptString($this->attributes['access_token']);
    }

    public function isTokenExpiringSoon(): bool
    {
        if (!$this->token_expires_at) return false;
        return $this->token_expires_at->lt(now()->addDays(7));
    }

    public function markUsed(): void
    {
        $this->update(['last_used_at' => now()]);
    }
}
