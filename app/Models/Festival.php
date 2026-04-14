<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Festival extends Model
{
    protected $fillable = [
        'slug', 'name', 'name_hindi', 'date', 'emoji', 'region', 'religion',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function scopeUpcoming($query, int $months = 3)
    {
        return $query->where('date', '>=', now()->toDateString())
                     ->where('date', '<=', now()->addMonths($months)->toDateString())
                     ->orderBy('date');
    }

    public function daysUntil(): int
    {
        return (int) now()->diffInDays($this->date, false);
    }
}
