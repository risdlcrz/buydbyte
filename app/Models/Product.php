<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'slug',
        'brand',
        'model',
        'description',
        'short_description',
        'sku',
        'price',
        'sale_price',
        'stock_quantity',
        'manage_stock',
        'in_stock',
        'images',
        'weight',
        'dimensions',
        'specifications',
        'key_features',
        'is_active',
        'is_featured',
        'category_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'manage_stock' => 'boolean',
        'in_stock' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'images' => 'array',
        'specifications' => 'array',
        'key_features' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
            if (empty($model->sku)) {
                $model->sku = 'SKU-' . strtoupper(Str::random(8));
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('name') && empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function cartItems()
    {
        return $this->hasMany(Cart::class, 'product_id', 'product_id');
    }

    public function comparisons()
    {
        return $this->hasMany(ProductComparison::class, 'product_id', 'product_id');
    }

    public function attributes()
    {
        return $this->hasMany(ProductAttribute::class, 'product_id', 'product_id');
    }

    public function activeAttributes()
    {
        return $this->attributes()
            ->whereHas('attributeDefinition', function ($query) {
                $query->where('is_active', true);
            })
            ->with('attributeDefinition');
    }

    public function getSortedActiveAttributesAttribute()
    {
        return $this->activeAttributes()->get()->sortBy(function($attribute) {
            return $attribute->attributeDefinition->sort_order ?? 999;
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getCurrentPriceAttribute()
    {
        return $this->sale_price ?? $this->price;
    }

    public function getIsOnSaleAttribute()
    {
        return !is_null($this->sale_price) && $this->sale_price < $this->price;
    }

    public function getDiscountPercentageAttribute()
    {
        if (!$this->is_on_sale) {
            return 0;
        }
        return round((($this->price - $this->sale_price) / $this->price) * 100);
    }

    public function getMainImageAttribute()
    {
        return $this->images[0] ?? null;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('in_stock', true);
    }

    /**
     * Check if product is in comparison list for session/user
     */
    public function isInComparison($sessionId = null, $userId = null)
    {
        $query = $this->comparisons();
        
        if ($userId) {
            $query->where('user_id', $userId);
        } elseif ($sessionId) {
            $query->where('session_id', $sessionId);
        }
        
        return $query->exists();
    }

    /**
     * Get formatted specifications for display
     */
    public function getFormattedSpecificationsAttribute()
    {
        if (!$this->specifications) {
            return [];
        }
        
        $formatted = [];
        foreach ($this->specifications as $key => $value) {
            $formatted[] = [
                'name' => ucwords(str_replace('_', ' ', $key)),
                'value' => $value
            ];
        }
        
        return $formatted;
    }
}
