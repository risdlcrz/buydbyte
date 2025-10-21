<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductAttribute extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_attribute_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'product_id',
        'attribute_id',
        'value',
        'numeric_value',
    ];

    protected $casts = [
        'numeric_value' => 'decimal:4',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->product_attribute_id)) {
                $model->product_attribute_id = Str::uuid();
            }
        });
    }

    /**
     * Get the product that owns this attribute
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    /**
     * Get the attribute definition
     */
    public function attributeDefinition()
    {
        return $this->belongsTo(AttributeDefinition::class, 'attribute_id', 'attribute_id');
    }

    /**
     * Get formatted value for display
     */
    public function getFormattedValueAttribute()
    {
        if ($this->attributeDefinition) {
            return $this->attributeDefinition->formatValue($this->value);
        }
        return $this->value;
    }

    /**
     * Get attributes for a product
     */
    public static function getProductAttributes($productId)
    {
        return static::with('attributeDefinition')
            ->where('product_id', $productId)
            ->whereHas('attributeDefinition', function ($query) {
                $query->where('is_active', true);
            })
            ->get()
            ->keyBy('attributeDefinition.slug');
    }

    /**
     * Set attribute value with automatic numeric conversion
     */
    public function setValue($value, AttributeDefinition $definition)
    {
        $this->value = $value;
        
        // Store numeric value for filtering/comparison
        if (in_array($definition->data_type, ['number', 'decimal']) && is_numeric($value)) {
            $this->numeric_value = $value;
        } else {
            $this->numeric_value = null;
        }
    }

    /**
     * Get comparable attributes for multiple products
     */
    public static function getComparableAttributes(array $productIds)
    {
        return static::with(['attributeDefinition', 'product'])
            ->whereIn('product_id', $productIds)
            ->whereHas('attributeDefinition', function ($query) {
                $query->where('is_active', true)
                      ->where('is_comparable', true);
            })
            ->get()
            ->groupBy('attributeDefinition.slug');
    }
}
