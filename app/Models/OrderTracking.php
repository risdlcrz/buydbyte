<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OrderTracking extends Model
{
    use HasFactory;

    protected $primaryKey = 'tracking_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'order_id',
        'status',
        'description',
        'location',
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
}