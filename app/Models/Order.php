<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'lyrics',
        'performer_name',
        'music_style',
        'plan',
        'cover_description',
        'cover_image_path',
        'status',
        'promo_code_id',
        'gift_certificate_code',
        'discount_amount',
        'amount_paid',
        'user_comment',
        'selected_audio_id',
        'selected_cover_id',
    ];

    const STATUSES = [
        'pending_payment'        => 'Ожидает оплаты',
        'canceled'               => 'Отменён',
        'new'                    => 'Новый',
        'in_progress'            => 'В работе',
        'generated'              => 'Песня сгенерирована',
        'sent_for_revision'      => 'Отправлен на доработку',
        'under_revision'         => 'На доработке',
        'publication_queue'      => 'В очереди на публикацию',
        'publishing'             => 'Публикация началась',
        'sent_to_distributor'    => 'Отправлен дистрибьютору',
        'approved_by_distributor'=> 'Одобрен дистрибьютором',
        'rejected_by_distributor'=> 'Отклонён дистрибьютором',
        'rejected_by_platforms'  => 'Отклонён площадками',
        'completed'              => 'Заказ выполнен',
    ];

    const PLANS = [
        1 => ['name' => 'Просто попробовать',                           'price' => 600],
        2 => ['name' => 'Хочу крутую песню',                           'price' => 5000],
        3 => ['name' => 'Хочу крутую песню и публикацию на площадках', 'price' => 15000],
    ];

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function planLabel(): string
    {
        return self::PLANS[$this->plan]['name'] ?? "План {$this->plan}";
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function files()
    {
        return $this->hasMany(OrderFile::class);
    }

    public function audioFiles()
    {
        return $this->hasMany(OrderFile::class)->where('type', 'audio');
    }

    public function coverFiles()
    {
        return $this->hasMany(OrderFile::class)->where('type', 'cover');
    }

    public function statusLogs()
    {
        return $this->hasMany(OrderStatusLog::class)->orderBy('created_at');
    }

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class)->orderBy('created_at');
    }

    public function promoCode()
    {
        return $this->belongsTo(PromoCode::class);
    }

    public function payment()
    {
        return $this->morphOne(Payment::class, 'payable');
    }

    public function upgrades()
    {
        return $this->hasMany(OrderUpgrade::class);
    }

    public function editRequests()
    {
        return $this->hasMany(EditRequest::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
}
