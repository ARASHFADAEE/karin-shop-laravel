<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ProductFilter extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'type',
        'description',
        'options',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'options' => 'array',
        'is_active' => 'boolean',
    ];

    // Filter types
    const TYPE_SELECT = 'select';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_RANGE = 'range';
    const TYPE_COLOR = 'color';
    const TYPE_SIZE = 'size';
    const TYPE_BRAND = 'brand';
    const TYPE_PRICE = 'price';

    // Relationships
    public function values(): HasMany
    {
        return $this->hasMany(ProductFilterValue::class, 'filter_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_filter_values', 'filter_id', 'product_id')
                    ->withPivot('value', 'display_value', 'numeric_value')
                    ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($filter) {
            if (empty($filter->slug)) {
                $filter->slug = Str::slug($filter->name);
            }
        });

        static::updating(function ($filter) {
            if ($filter->isDirty('name') && empty($filter->slug)) {
                $filter->slug = Str::slug($filter->name);
            }
        });
    }

    // Utility Methods
    public function getTypeLabel(): string
    {
        return match($this->type) {
            self::TYPE_SELECT => 'انتخاب تکی',
            self::TYPE_CHECKBOX => 'انتخاب چندگانه',
            self::TYPE_RANGE => 'محدوده',
            self::TYPE_COLOR => 'رنگ',
            self::TYPE_SIZE => 'سایز',
            self::TYPE_BRAND => 'برند',
            self::TYPE_PRICE => 'قیمت',
            default => $this->type
        };
    }

    public function getUniqueValues(): array
    {
        return $this->values()
                   ->select('value', 'display_value')
                   ->distinct()
                   ->orderBy('display_value')
                   ->get()
                   ->map(function($item) {
                       return [
                           'value' => $item->value,
                           'label' => $item->display_value ?: $item->value,
                           'count' => $this->getValueCount($item->value)
                       ];
                   })
                   ->toArray();
    }

    public function getValueCount(string $value): int
    {
        return $this->values()->where('value', $value)->count();
    }

    public function getPriceRange(): array
    {
        if ($this->type !== self::TYPE_PRICE) {
            return ['min' => 0, 'max' => 0];
        }

        $values = $this->values()
                      ->whereNotNull('numeric_value')
                      ->selectRaw('MIN(numeric_value) as min_price, MAX(numeric_value) as max_price')
                      ->first();

        return [
            'min' => $values->min_price ?? 0,
            'max' => $values->max_price ?? 0
        ];
    }

    // Static Methods
    public static function getTypeOptions(): array
    {
        return [
            self::TYPE_SELECT => 'انتخاب تکی',
            self::TYPE_CHECKBOX => 'انتخاب چندگانه',
            self::TYPE_RANGE => 'محدوده',
            self::TYPE_COLOR => 'رنگ',
            self::TYPE_SIZE => 'سایز',
            self::TYPE_BRAND => 'برند',
            self::TYPE_PRICE => 'قیمت',
        ];
    }

    public static function createDefaultFilters(): void
    {
        $defaultFilters = [
            [
                'name' => 'برند',
                'slug' => 'brand',
                'type' => self::TYPE_SELECT,
                'description' => 'فیلتر بر اساس برند محصول',
                'sort_order' => 1
            ],
            [
                'name' => 'قیمت',
                'slug' => 'price',
                'type' => self::TYPE_RANGE,
                'description' => 'فیلتر بر اساس محدوده قیمت',
                'sort_order' => 2
            ],
            [
                'name' => 'رنگ',
                'slug' => 'color',
                'type' => self::TYPE_COLOR,
                'description' => 'فیلتر بر اساس رنگ محصول',
                'sort_order' => 3
            ],
            [
                'name' => 'سایز',
                'slug' => 'size',
                'type' => self::TYPE_CHECKBOX,
                'description' => 'فیلتر بر اساس سایز محصول',
                'sort_order' => 4
            ],
            [
                'name' => 'موجودی',
                'slug' => 'availability',
                'type' => self::TYPE_CHECKBOX,
                'description' => 'فیلتر بر اساس وضعیت موجودی',
                'options' => [
                    ['value' => 'in_stock', 'label' => 'موجود'],
                    ['value' => 'out_of_stock', 'label' => 'ناموجود']
                ],
                'sort_order' => 5
            ]
        ];

        foreach ($defaultFilters as $filter) {
            self::firstOrCreate(
                ['slug' => $filter['slug']],
                $filter
            );
        }
    }

    public function isRangeType(): bool
    {
        return in_array($this->type, [self::TYPE_RANGE, self::TYPE_PRICE]);
    }

    public function isMultiSelect(): bool
    {
        return in_array($this->type, [self::TYPE_CHECKBOX]);
    }

    public function isSingleSelect(): bool
    {
        return in_array($this->type, [self::TYPE_SELECT, self::TYPE_BRAND]);
    }

    public function isColorType(): bool
    {
        return $this->type === self::TYPE_COLOR;
    }
}