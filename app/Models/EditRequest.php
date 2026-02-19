<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EditRequest extends Model
{
    protected $fillable = ['order_id', 'instructions', 'status'];

    const PRICE = 400;

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function payment()
    {
        return $this->morphOne(Payment::class, 'payable');
    }
}
