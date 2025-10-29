<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OrderItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'order_item_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'price',
        'quantity',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
        });
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}