<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductComparison extends Model
{
    protected $primaryKey = 'comparison_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'session_id',
        'user_id',
        'product_id',
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

    /**
     * Get the user that owns the comparison
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the product being compared
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    /**
     * Scope to get comparisons for a specific session
     */
    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Scope to get comparisons for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get comparison list for current session/user
     */
    public static function getComparisonList($sessionId = null, $userId = null)
    {
        $query = static::with('product.category');
        
        if ($userId) {
            $query->where('user_id', $userId);
        } elseif ($sessionId) {
            $query->where('session_id', $sessionId);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get count of items in comparison for session/user
     */
    public static function getComparisonCount($sessionId = null, $userId = null)
    {
        $query = static::query();
        
        if ($userId) {
            $query->where('user_id', $userId);
        } elseif ($sessionId) {
            $query->where('session_id', $sessionId);
        }
        
        return $query->count();
    }
}
