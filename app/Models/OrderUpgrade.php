<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderUpgrade extends Model
{
    protected $fillable = ['order_id', 'from_plan', 'to_plan', 'amount', 'status'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function payment()
    {
        return $this->morphOne(Payment::class, 'payable');
    }
}
