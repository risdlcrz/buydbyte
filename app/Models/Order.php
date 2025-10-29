<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $primaryKey = 'order_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'status',
        'shipping_method',
        'shipping_cost',
        'subtotal',
        'total',
        'tracking_number',
        'shipping_address',
        'payment_intent_id',
    ];

    protected $casts = [
        'shipping_cost' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
        'shipping_address' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
            if (empty($model->tracking_number)) {
                $model->tracking_number = 'TRK' . strtoupper(Str::random(10));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    public function tracking()
    {
        return $this->hasMany(OrderTracking::class, 'order_id', 'order_id');
    }
}