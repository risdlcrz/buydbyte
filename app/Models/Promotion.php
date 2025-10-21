<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Promotion extends Model
{
    use HasFactory;

    protected $primaryKey = 'promotion_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'title',
        'description',
        'type',
        'banner_image',
        'background_color',
        'text_color',
        'button_text',
        'button_link',
        'button_color',
        'discount_percentage',
        'discount_amount',
        'discount_code',
        'discount_text',
        'start_date',
        'end_date',
        'is_active',
        'sort_order',
        'target_audience',
        'display_pages',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'display_pages' => 'array',
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
     * Scope to get active promotions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    /**
     * Scope to get promotions for a specific page
     */
    public function scopeForPage($query, $page)
    {
        return $query->where(function($q) use ($page) {
            $q->whereNull('display_pages')
              ->orWhereJsonContains('display_pages', $page)
              ->orWhereJsonContains('display_pages', 'all');
        });
    }

    /**
     * Scope to get promotions for target audience
     */
    public function scopeForAudience($query, $user = null)
    {
        return $query->where(function($q) use ($user) {
            $q->where('target_audience', 'all');
            
            if ($user) {
                // Returning user
                $q->orWhere('target_audience', 'returning_users');
            } else {
                // New/guest user
                $q->orWhere('target_audience', 'new_users');
            }
        });
    }

    /**
     * Scope to order by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }

    /**
     * Check if promotion is currently active
     */
    public function getIsCurrentlyActiveAttribute()
    {
        return $this->is_active 
               && $this->start_date <= now() 
               && $this->end_date >= now();
    }

    /**
     * Get the formatted discount text
     */
    public function getDiscountTextAttribute()
    {
        if ($this->discount_percentage) {
            return "{$this->discount_percentage}% OFF";
        }
        if ($this->discount_amount) {
            return "$" . number_format($this->discount_amount, 2) . " OFF";
        }
        return null;
    }
}
