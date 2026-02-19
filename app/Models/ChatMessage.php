<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = ['order_id', 'user_id', 'is_admin', 'body'];

    protected function casts(): array
    {
        return [
            'is_admin' => 'boolean',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
