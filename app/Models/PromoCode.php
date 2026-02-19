<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    protected $fillable = ['code', 'discount_percent', 'max_uses', 'used_count', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return false;
        }

        return true;
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
