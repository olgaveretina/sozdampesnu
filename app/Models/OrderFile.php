<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderFile extends Model
{
    protected $fillable = ['order_id', 'type', 'path', 'label'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
