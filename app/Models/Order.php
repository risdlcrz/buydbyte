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
        'order_number',
        'payment_status',
        'payment_method',
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
            if (empty($model->order_number)) {
                // Generate unique order number
                $maxAttempts = 10;
                $attempt = 0;
                do {
                    $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(8));
                    $exists = static::where('order_number', $orderNumber)->exists();
                    $attempt++;
                } while ($exists && $attempt < $maxAttempts);
                
                if ($exists) {
                    // Fallback to timestamp-based number if random fails
                    $orderNumber = 'ORD-' . date('YmdHis') . '-' . strtoupper(Str::random(4));
                }
                
                $model->order_number = $orderNumber;
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

    public function feedback()
    {
        return $this->hasOne(Feedback::class, 'order_id', 'order_id');
    }

    public function canBeCompleted()
    {
        return $this->status === 'delivered' && !$this->feedback;
    }
}