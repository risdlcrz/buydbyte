<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AttributeDefinition extends Model
{
    use HasFactory;

    protected $primaryKey = 'attribute_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'slug',
        'display_name',
        'description',
        'data_type',
        'unit',
        'validation_rules',
        'possible_values',
        'applicable_categories',
        'attribute_group',
        'sort_order',
        'is_active',
        'is_filterable',
        'is_comparable',
        'is_required',
    ];

    protected $casts = [
        'validation_rules' => 'array',
        'possible_values' => 'array',
        'applicable_categories' => 'array',
        'is_active' => 'boolean',
        'is_filterable' => 'boolean',
        'is_comparable' => 'boolean',
        'is_required' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->attribute_id)) {
                $model->attribute_id = Str::uuid();
            }
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    /**
     * Get product attributes for this definition
     */
    public function productAttributes()
    {
        return $this->hasMany(ProductAttribute::class, 'attribute_id', 'attribute_id');
    }

    /**
     * Get attributes for a specific category
     */
    public static function forCategory($category)
    {
        return static::where('is_active', true)
            ->where(function ($query) use ($category) {
                $query->whereJsonContains('applicable_categories', 'all')
                      ->orWhereJsonContains('applicable_categories', $category);
            })
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get filterable attributes for a category
     */
    public static function filterableForCategory($category)
    {
        return static::forCategory($category)
            ->where('is_filterable', true);
    }

    /**
     * Get comparable attributes for a category
     */
    public static function comparableForCategory($category)
    {
        return static::forCategory($category)
            ->where('is_comparable', true);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'attribute_id';
    }

    /**
     * Validate a value against this attribute's rules
     */
    public function validateValue($value)
    {
        // Basic validation based on data type
        switch ($this->data_type) {
            case 'number':
            case 'decimal':
                return is_numeric($value);
            case 'boolean':
                return is_bool($value) || in_array($value, [0, 1, '0', '1', 'true', 'false']);
            case 'select':
                return $this->possible_values ? in_array($value, $this->possible_values) : true;
            case 'text':
            default:
                return is_string($value) || is_null($value);
        }
    }

    /**
     * Format value for display
     */
    public function formatValue($value)
    {
        if (is_null($value)) {
            return null;
        }

        switch ($this->data_type) {
            case 'number':
                return number_format($value) . ($this->unit ? ' ' . $this->unit : '');
            case 'decimal':
                return number_format($value, 2) . ($this->unit ? ' ' . $this->unit : '');
            case 'boolean':
                return $value ? 'Yes' : 'No';
            case 'select':
            case 'text':
            default:
                return $value . ($this->unit ? ' ' . $this->unit : '');
        }
    }
}
