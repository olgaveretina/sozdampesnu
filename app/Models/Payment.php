<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'payable_type',
        'payable_id',
        'amount',
        'yookassa_id',
        'status',
        'yookassa_data',
    ];

    protected function casts(): array
    {
        return [
            'yookassa_data' => 'array',
        ];
    }

    public function payable()
    {
        return $this->morphTo();
    }
}
