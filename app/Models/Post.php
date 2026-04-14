<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'platforms', 'connected_account_ids',
        'caption', 'image_url', 'image_path',
        'status', 'scheduled_at', 'published_at',
        'platform_post_ids', 'retry_count', 'failure_reason',
        'ai_generated', 'festival', 'language',
    ];

    protected $casts = [
        'platforms'            => 'array',
        'connected_account_ids' => 'array',
        'platform_post_ids'    => 'array',
        'scheduled_at'         => 'datetime',
        'published_at'         => 'datetime',
        'ai_generated'         => 'boolean',
        'retry_count'          => 'integer',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeDue($query)
    {
        return $query->where('status', 'scheduled')
                     ->where('scheduled_at', '<=', now()->addMinutes(2));
    }

    public function scopeForMonth($query, int $year, int $month)
    {
        return $query->whereYear('scheduled_at', $year)
                     ->whereMonth('scheduled_at', $month);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function isEditable(): bool
    {
        return in_array($this->status, ['draft', 'scheduled'])
            && $this->scheduled_at->gt(now()->addMinutes(5));
    }

    public function captionSnippet(int $length = 80): string
    {
        return strlen($this->caption) > $length
            ? substr($this->caption, 0, $length) . '...'
            : $this->caption;
    }

    public function scheduledAtIst(): string
    {
        return $this->scheduled_at->timezone('Asia/Kolkata')->format('d M Y, h:i A');
    }
}
