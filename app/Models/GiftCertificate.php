<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GiftCertificate extends Model
{
    protected $fillable = [
        'code',
        'amount_rub',
        'is_used',
        'buyer_user_id',
        'used_by_order_id',
        'used_at',
    ];

    protected function casts(): array
    {
        return [
            'is_used' => 'boolean',
            'used_at' => 'datetime',
        ];
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_user_id');
    }

    public function usedByOrder()
    {
        return $this->belongsTo(Order::class, 'used_by_order_id');
    }

    public function payment()
    {
        return $this->morphOne(Payment::class, 'payable');
    }
}
