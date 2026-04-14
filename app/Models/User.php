<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'photo_url', 'google_id',
        'plan', 'post_quota', 'posts_used_this_month',
        'quota_reset_date', 'account_limit',
        'total_posts_scheduled', 'total_posts_published',
        'onboarding_complete', 'last_active_at',
    ];

    protected $hidden = ['remember_token'];

    protected $casts = [
        'onboarding_complete' => 'boolean',
        'quota_reset_date'    => 'date',
        'last_active_at'      => 'datetime',
        'post_quota'          => 'integer',
        'posts_used_this_month' => 'integer',
        'account_limit'       => 'integer',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function connectedAccounts()
    {
        return $this->hasMany(ConnectedAccount::class)->where('is_active', true);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // ─── Helper Methods ───────────────────────────────────────────────────────

    public function isUnlimited(): bool
    {
        return $this->post_quota >= 999999;
    }

    public function postsRemaining(): int
    {
        if ($this->isUnlimited()) return 999999;
        return max(0, $this->post_quota - $this->posts_used_this_month);
    }

    public function canScheduleMore(): bool
    {
        return $this->isUnlimited() || $this->posts_used_this_month < $this->post_quota;
    }

    public function checkAndResetQuota(): void
    {
        if ($this->quota_reset_date && now()->gte($this->quota_reset_date)) {
            $this->update([
                'posts_used_this_month' => 0,
                'quota_reset_date'      => now()->addMonth()->startOfMonth()->toDateString(),
            ]);
        }
    }

    public function upgradePlan(string $planId): void
    {
        $plan = config("postpilot.plans.{$planId}");
        if (!$plan) return;

        $this->update([
            'plan'          => $planId,
            'post_quota'    => $plan['post_quota'],
            'account_limit' => $plan['account_limit'],
        ]);
    }

    public function touchActivity(): void
    {
        $this->update(['last_active_at' => now()]);
    }
}
