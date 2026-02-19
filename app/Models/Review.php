<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['order_id', 'user_id', 'text', 'rating', 'is_published'];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
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
