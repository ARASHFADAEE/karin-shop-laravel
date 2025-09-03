<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductFilterValue extends Model
{
    protected $fillable = [
        'product_id',
        'filter_id',
        'value',
        'display_value',
        'numeric_value'
    ];

    protected $casts = [
        'numeric_value' => 'decimal:2',
    ];

    // Relationships
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function filter(): BelongsTo
    {
        return $this->belongsTo(ProductFilter::class, 'filter_id');
    }

    // Scopes
    public function scopeForFilter($query, $filterId)
    {
        return $query->where('filter_id', $filterId);
    }

    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeWithValue($query, $value)
    {
        return $query->where('value', $value);
    }

    public function scopeInRange($query, $min, $max)
    {
        return $query->whereBetween('numeric_value', [$min, $max]);
    }

    // Utility Methods
    public function getDisplayLabel(): string
    {
        return $this->display_value ?: $this->value;
    }

    public function isNumeric(): bool
    {
        return !is_null($this->numeric_value);
    }

    // Static Methods
    public static function addFilterValue(Product $product, ProductFilter $filter, string $value, ?string $displayValue = null, ?float $numericValue = null): self
    {
        return self::updateOrCreate(
            [
                'product_id' => $product->id,
                'filter_id' => $filter->id,
                'value' => $value
            ],
            [
                'display_value' => $displayValue,
                'numeric_value' => $numericValue
            ]
        );
    }

    public static function removeFilterValue(Product $product, ProductFilter $filter, string $value): bool
    {
        return self::where('product_id', $product->id)
                  ->where('filter_id', $filter->id)
                  ->where('value', $value)
                  ->delete() > 0;
    }

    public static function getProductsByFilter(ProductFilter $filter, $values): \Illuminate\Database\Eloquent\Collection
    {
        if (!is_array($values)) {
            $values = [$values];
        }

        return Product::whereHas('filterValues', function($query) use ($filter, $values) {
            $query->where('filter_id', $filter->id)
                  ->whereIn('value', $values);
        })->get();
    }

    public static function getProductsByPriceRange(float $minPrice, float $maxPrice): \Illuminate\Database\Eloquent\Collection
    {
        return Product::whereHas('filterValues', function($query) use ($minPrice, $maxPrice) {
            $query->whereHas('filter', function($filterQuery) {
                $filterQuery->where('type', ProductFilter::TYPE_PRICE);
            })->whereBetween('numeric_value', [$minPrice, $maxPrice]);
        })->get();
    }

    public static function syncProductFilters(Product $product, array $filters): void
    {
        // حذف فیلترهای قبلی
        self::where('product_id', $product->id)->delete();

        // اضافه کردن فیلترهای جدید
        foreach ($filters as $filterId => $values) {
            $filter = ProductFilter::find($filterId);
            if (!$filter) continue;

            if (!is_array($values)) {
                $values = [$values];
            }

            foreach ($values as $valueData) {
                if (is_string($valueData)) {
                    $valueData = ['value' => $valueData];
                }

                self::addFilterValue(
                    $product,
                    $filter,
                    $valueData['value'],
                    $valueData['display_value'] ?? null,
                    $valueData['numeric_value'] ?? null
                );
            }
        }
    }
}