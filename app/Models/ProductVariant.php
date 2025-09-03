<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'sku',
        'name',
        'price',
        'original_price',
        'stock',
        'weight',
        'dimensions',
        'color',
        'size',
        'material',
        'brand',
        'status',
        'is_default',
        'sort_order',
        'attributes'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'dimensions' => 'array',
        'attributes' => 'array',
        'is_default' => 'boolean',
        'stock' => 'integer',
        'sort_order' => 'integer'
    ];

    // Relationships
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // Accessors & Mutators
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price) . ' تومان';
    }

    public function getDiscountPercentageAttribute(): ?float
    {
        if ($this->original_price && $this->original_price > $this->price) {
            return round((($this->original_price - $this->price) / $this->original_price) * 100, 2);
        }
        return null;
    }

    public function getIsOnSaleAttribute(): bool
    {
        return $this->original_price && $this->original_price > $this->price;
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->stock <= 0) {
            return 'out_of_stock';
        } elseif ($this->stock <= 5) {
            return 'low_stock';
        }
        return 'in_stock';
    }

    public function getStockStatusLabelAttribute(): string
    {
        return match($this->stock_status) {
            'out_of_stock' => 'ناموجود',
            'low_stock' => 'موجودی کم',
            'in_stock' => 'موجود',
            default => 'نامشخص'
        };
    }

    // Methods
    public function generateSku(): string
    {
        $baseSku = $this->product->sku ?? 'PRD';
        $variantSuffix = strtoupper(substr($this->color ?? 'DEF', 0, 3)) . 
                        strtoupper(substr($this->size ?? 'STD', 0, 3));
        
        return $baseSku . '-' . $variantSuffix;
    }

    public function updateStock(int $quantity, string $operation = 'subtract'): bool
    {
        if ($operation === 'subtract') {
            if ($this->stock < $quantity) {
                return false;
            }
            $this->stock -= $quantity;
        } else {
            $this->stock += $quantity;
        }

        return $this->save();
    }

    public function isAvailable(): bool
    {
        return $this->status === 'active' && $this->stock > 0;
    }

    public function getDisplayName(): string
    {
        $parts = [];
        
        if ($this->name) {
            $parts[] = $this->name;
        }
        
        if ($this->color) {
            $parts[] = 'رنگ: ' . $this->color;
        }
        
        if ($this->size) {
            $parts[] = 'سایز: ' . $this->size;
        }
        
        return implode(' - ', $parts) ?: 'پیش‌فرض';
    }

    // Static methods
    public static function getColorOptions(): array
    {
        return [
            'سفید' => 'سفید',
            'مشکی' => 'مشکی',
            'قرمز' => 'قرمز',
            'آبی' => 'آبی',
            'سبز' => 'سبز',
            'زرد' => 'زرد',
            'نارنجی' => 'نارنجی',
            'بنفش' => 'بنفش',
            'صورتی' => 'صورتی',
            'خاکستری' => 'خاکستری',
            'قهوه‌ای' => 'قهوه‌ای',
            'طلایی' => 'طلایی',
            'نقره‌ای' => 'نقره‌ای'
        ];
    }

    public static function getSizeOptions(): array
    {
        return [
            'XS' => 'XS',
            'S' => 'S',
            'M' => 'M',
            'L' => 'L',
            'XL' => 'XL',
            'XXL' => 'XXL',
            'XXXL' => 'XXXL',
            '36' => '36',
            '37' => '37',
            '38' => '38',
            '39' => '39',
            '40' => '40',
            '41' => '41',
            '42' => '42',
            '43' => '43',
            '44' => '44',
            '45' => '45'
        ];
    }

    public static function getStatusOptions(): array
    {
        return [
            'active' => 'فعال',
            'inactive' => 'غیرفعال',
            'discontinued' => 'متوقف شده'
        ];
    }
}