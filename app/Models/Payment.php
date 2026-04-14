<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id', 'payment_id', 'order_id',
        'plan', 'amount', 'currency', 'status', 'signature',
    ];

    protected $casts = [
        'amount' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function amountInr(): string
    {
        return '₹' . number_format($this->amount / 100, 0);
    }
}
