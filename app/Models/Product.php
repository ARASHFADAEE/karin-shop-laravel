<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image',
        'price',
        'original_price',
        'discount_percentage',
        'discount_amount',
        'has_discount',
        'discount_starts_at',
        'discount_ends_at',
        'stock',
        'sku',
        'status',
        // Inventory Management
        'stock_quantity',
        'low_stock_threshold',
        'track_inventory',
        'allow_backorder',
        'reserved_quantity',
        'stock_status',
        'barcode',
        'weight',
        'dimensions',
        'supplier_name',
        'supplier_sku',
        'cost_price',
        'reorder_point',
        'reorder_quantity',
        'maximum_stock',
        'warehouse_location',
        'shelf_location',
        'last_stock_update',
        'last_reorder_date',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'has_discount' => 'boolean',
        'discount_starts_at' => 'datetime',
        'discount_ends_at' => 'datetime',
        // Inventory Management
        'track_inventory' => 'boolean',
        'allow_backorder' => 'boolean',
        'weight' => 'decimal:2',
        'dimensions' => 'array',
        'cost_price' => 'decimal:2',
        'last_stock_update' => 'datetime',
        'last_reorder_date' => 'datetime',
    ];

    // Auto-generate slug when creating
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
                
                // Ensure unique slug
                $originalSlug = $product->slug;
                $counter = 1;
                while (static::where('slug', $product->slug)->exists()) {
                    $product->slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }
        });
        
        static::deleting(function ($product) {
            // حذف cart items مرتبط با این محصول
            $product->cartItems()->delete();
        });
    }

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Many-to-many relationship with categories
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories')
                    ->withPivot('is_primary')
                    ->withTimestamps();
    }

    // Get primary category
    public function primaryCategory()
    {
        return $this->categories()->wherePivot('is_primary', true)->first();
    }

    public function featuredImages()
    {
        return $this->hasMany(ProductFeaturedImage::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
    
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function attributes()
    {
        return $this->hasMany(ProductAttribute::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function seoMeta()
    {
        return $this->morphOne(SeoMeta::class, 'metaable');
    }

    // SEO Helper Methods
    public function getMetaTitleAttribute($value)
    {
        return $value ?: $this->name;
    }

    public function getOgDescriptionAttribute($value)
    {
        return $value ?: Str::limit(strip_tags($this->description), 160);
    }

    // Discount Methods
    public function isDiscountActive()
    {
        if (!$this->has_discount) {
            return false;
        }

        $now = now();
        
        // Check if discount period is valid
        if ($this->discount_starts_at && $now->lt($this->discount_starts_at)) {
            return false;
        }
        
        if ($this->discount_ends_at && $now->gt($this->discount_ends_at)) {
            return false;
        }
        
        return true;
    }

    public function getDiscountedPrice()
    {
        if (!$this->isDiscountActive()) {
            return $this->price;
        }

        $basePrice = $this->original_price ?: $this->price;
        
        if ($this->discount_percentage) {
            return $basePrice - ($basePrice * ($this->discount_percentage / 100));
        }
        
        if ($this->discount_amount) {
            return max(0, $basePrice - $this->discount_amount);
        }
        
        return $this->price;
    }

    public function getOriginalPriceAttribute($value)
    {
        return $value ?: $this->price;
    }

    public function getFinalPrice()
    {
        return $this->isDiscountActive() ? $this->getDiscountedPrice() : $this->price;
    }

    public function getDiscountPercentageValue()
    {
        if (!$this->isDiscountActive() || !$this->original_price) {
            return 0;
        }
        
        $originalPrice = $this->original_price;
        $discountedPrice = $this->getDiscountedPrice();
        
        return round((($originalPrice - $discountedPrice) / $originalPrice) * 100, 2);
    }

    public function getSavingsAmount()
    {
        if (!$this->isDiscountActive()) {
            return 0;
        }
        
        $originalPrice = $this->original_price ?: $this->price;
        return $originalPrice - $this->getDiscountedPrice();
    }

    // Scope for products with active discounts
    public function scopeWithActiveDiscount($query)
    {
        return $query->where('has_discount', true)
                    ->where(function($q) {
                        $q->whereNull('discount_starts_at')
                          ->orWhere('discount_starts_at', '<=', now());
                    })
                    ->where(function($q) {
                        $q->whereNull('discount_ends_at')
                          ->orWhere('discount_ends_at', '>=', now());
                    });
    }

    public function getOgTitleAttribute($value)
    {
        return $value ?: $this->meta_title;
    }

    public function getMetaDescriptionAttribute($value)
    {
        return $value ?: Str::limit(strip_tags($this->description), 160);
    }

    public function getOgImageAttribute($value)
    {
        if ($value) {
            return $value;
        }
        
        $featuredImage = $this->featuredImages()->first();
        return $featuredImage ? $featuredImage->image_url : null;
    }

    // ==================== INVENTORY MANAGEMENT ====================

    /**
     * Get inventory movements for this product
     */
    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    /**
     * Get inventory alerts for this product
     */
    public function inventoryAlerts()
    {
        return $this->hasMany(InventoryAlert::class);
    }

    /**
     * Get active inventory alerts
     */
    public function activeInventoryAlerts()
    {
        return $this->inventoryAlerts()->where('status', 'active');
    }

    /**
     * Get available quantity (stock - reserved)
     */
    public function getAvailableQuantityAttribute(): int
    {
        return max(0, ($this->stock_quantity ?? 0) - ($this->reserved_quantity ?? 0));
    }

    /**
     * Check if product is in stock
     */
    public function isInStock(): bool
    {
        if (!$this->track_inventory) {
            return true;
        }
        
        return $this->available_quantity > 0 || $this->allow_backorder;
    }

    /**
     * Check if product is low stock
     */
    public function isLowStock(): bool
    {
        if (!$this->track_inventory) {
            return false;
        }
        
        return $this->stock_quantity <= ($this->low_stock_threshold ?? 5);
    }

    /**
     * Check if product needs reorder
     */
    public function needsReorder(): bool
    {
        if (!$this->track_inventory) {
            return false;
        }
        
        return $this->stock_quantity <= ($this->reorder_point ?? 10);
    }

    /**
     * Update stock status based on current quantity
     */
    public function updateStockStatus(): void
    {
        if (!$this->track_inventory) {
            $this->update(['stock_status' => 'in_stock']);
            return;
        }

        $status = 'in_stock';
        
        if ($this->stock_quantity <= 0) {
            $status = $this->allow_backorder ? 'on_backorder' : 'out_of_stock';
        } elseif ($this->isLowStock()) {
            $status = 'low_stock';
        }

        $this->update([
            'stock_status' => $status,
            'last_stock_update' => now()
        ]);

        // Check and create alerts
        InventoryAlert::checkProductAlerts($this);
    }

    /**
     * Add stock to product
     */
    public function addStock(int $quantity, ?User $user = null, array $data = []): InventoryMovement
    {
        return InventoryMovement::createMovement(
            $this,
            InventoryMovement::TYPE_IN,
            $quantity,
            $user,
            array_merge($data, ['reason' => 'Stock added'])
        );
    }

    /**
     * Remove stock from product
     */
    public function removeStock(int $quantity, ?User $user = null, array $data = []): InventoryMovement
    {
        return InventoryMovement::createMovement(
            $this,
            InventoryMovement::TYPE_OUT,
            $quantity,
            $user,
            array_merge($data, ['reason' => 'Stock removed'])
        );
    }

    /**
     * Adjust stock to specific quantity
     */
    public function adjustStock(int $newQuantity, ?User $user = null, string $reason = 'Stock adjustment'): InventoryMovement
    {
        return InventoryMovement::createMovement(
            $this,
            InventoryMovement::TYPE_ADJUSTMENT,
            $newQuantity,
            $user,
            ['reason' => $reason]
        );
    }

    /**
     * Reserve stock for an order
     */
    public function reserveStock(int $quantity): bool
    {
        if (!$this->track_inventory) {
            return true;
        }

        if ($this->available_quantity < $quantity && !$this->allow_backorder) {
            return false;
        }

        $this->increment('reserved_quantity', $quantity);
        $this->updateStockStatus();
        
        return true;
    }

    /**
     * Release reserved stock
     */
    public function releaseStock(int $quantity): void
    {
        if (!$this->track_inventory) {
            return;
        }

        $this->decrement('reserved_quantity', min($quantity, $this->reserved_quantity ?? 0));
        $this->updateStockStatus();
    }

    /**
     * Fulfill reserved stock (convert reservation to actual sale)
     */
    public function fulfillStock(int $quantity, ?User $user = null, array $data = []): InventoryMovement
    {
        // Release the reservation
        $this->releaseStock($quantity);
        
        // Create outbound movement for the sale
        return $this->removeStock($quantity, $user, array_merge($data, [
            'reason' => 'Order fulfillment'
        ]));
    }

    /**
     * Get stock status label
     */
    public function getStockStatusLabel(): string
    {
        return match($this->stock_status) {
            'in_stock' => 'موجود',
            'low_stock' => 'موجودی کم',
            'out_of_stock' => 'ناموجود',
            'on_backorder' => 'پیش‌سفارش',
            default => 'نامشخص'
        };
    }

    /**
     * Get stock status color for UI
     */
    public function getStockStatusColor(): string
    {
        return match($this->stock_status) {
            'in_stock' => 'green',
            'low_stock' => 'yellow',
            'out_of_stock' => 'red',
            'on_backorder' => 'blue',
            default => 'gray'
        };
    }

    /**
     * Scope for products that need reorder
     */
    public function scopeNeedsReorder($query)
    {
        return $query->where('track_inventory', true)
                    ->whereColumn('stock_quantity', '<=', 'reorder_point');
    }

    /**
     * Scope for low stock products
     */
    public function scopeLowStock($query)
    {
        return $query->where('track_inventory', true)
                    ->whereColumn('stock_quantity', '<=', 'low_stock_threshold');
    }

    /**
     * Scope for out of stock products
     */
    public function scopeFilterOutOfStock($query)
    {
        return $query->where('track_inventory', true)
                    ->where('stock_quantity', '<=', 0);
    }

    /**
     * Get profit margin
     */
    public function getProfitMargin(): ?float
    {
        if (!$this->cost_price || $this->cost_price <= 0) {
            return null;
        }
        
        $sellingPrice = $this->getDiscountedPrice();
        return (($sellingPrice - $this->cost_price) / $this->cost_price) * 100;
    }

    /**
     * Get profit amount
     */
    public function getProfitAmount(): ?float
    {
        if (!$this->cost_price) {
            return null;
        }
        
        return $this->getDiscountedPrice() - $this->cost_price;
    }

    /**
     * Boot method to handle model events
     */
    protected static function bootInventoryManagement()
    {
        static::created(function ($product) {
            if ($product->track_inventory) {
                $product->updateStockStatus();
            }
        });

        static::updated(function ($product) {
            if ($product->track_inventory && $product->wasChanged('stock_quantity')) {
                $product->updateStockStatus();
            }
        });
    }

    // Filter Relationships
    public function filterValues()
    {
        return $this->hasMany(ProductFilterValue::class);
    }

    public function filters()
    {
        return $this->belongsToMany(ProductFilter::class, 'product_filter_values', 'product_id', 'filter_id')
                    ->withPivot('value', 'display_value', 'numeric_value')
                    ->withTimestamps();
    }

    // Product Variants
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function activeVariants()
    {
        return $this->hasMany(ProductVariant::class)->where('status', 'active');
    }

    public function defaultVariant()
    {
        return $this->hasOne(ProductVariant::class)->where('is_default', true);
    }

    public function availableVariants()
    {
        return $this->hasMany(ProductVariant::class)
                    ->where('status', 'active')
                    ->where('stock', '>', 0);
    }

    // Filter Methods
    public function addFilterValue(ProductFilter $filter, string $value, ?string $displayValue = null, ?float $numericValue = null)
    {
        return ProductFilterValue::addFilterValue($this, $filter, $value, $displayValue, $numericValue);
    }

    public function removeFilterValue(ProductFilter $filter, string $value): bool
    {
        return ProductFilterValue::removeFilterValue($this, $filter, $value);
    }

    public function syncFilters(array $filters): void
    {
        ProductFilterValue::syncProductFilters($this, $filters);
    }

    public function getFilterValue(ProductFilter $filter)
    {
        return $this->filterValues()->where('filter_id', $filter->id)->first();
    }

    public function hasFilterValue(ProductFilter $filter, string $value): bool
    {
        return $this->filterValues()
                   ->where('filter_id', $filter->id)
                   ->where('value', $value)
                   ->exists();
    }

    // Advanced Filter Scopes
    public function scopeWithFilters($query, array $filters)
    {
        foreach ($filters as $filterId => $values) {
            if (empty($values)) continue;
            
            if (!is_array($values)) {
                $values = [$values];
            }
            
            $query->whereHas('filterValues', function($q) use ($filterId, $values) {
                $q->where('filter_id', $filterId)
                  ->whereIn('value', $values);
            });
        }
        
        return $query;
    }

    public function scopeWithPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('price', [$minPrice, $maxPrice]);
    }

    public function scopeInStock($query)
    {
        return $query->where(function($q) {
            $q->where('track_inventory', false)
              ->orWhere('stock_quantity', '>', 0);
        });
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('track_inventory', true)
                    ->where('stock_quantity', '<=', 0);
    }

    public function scopeByBrand($query, $brand)
    {
        return $query->whereHas('filterValues', function($q) use ($brand) {
            $q->whereHas('filter', function($filterQuery) {
                $filterQuery->where('slug', 'brand');
            })->where('value', $brand);
        });
    }

    public function scopeByColor($query, $color)
    {
        return $query->whereHas('filterValues', function($q) use ($color) {
            $q->whereHas('filter', function($filterQuery) {
                $filterQuery->where('slug', 'color');
            })->where('value', $color);
        });
    }

    public function scopeBySize($query, $size)
    {
        return $query->whereHas('filterValues', function($q) use ($size) {
            $q->whereHas('filter', function($filterQuery) {
                $filterQuery->where('slug', 'size');
            })->where('value', $size);
        });
    }

    // Auto-generate filter values
    public function generateAutoFilters(): void
    {
        // Auto-generate price filter
        $priceFilter = ProductFilter::where('slug', 'price')->first();
        if ($priceFilter && $this->price) {
            $this->addFilterValue($priceFilter, (string)$this->price, null, $this->price);
        }

        // Auto-generate availability filter
        $availabilityFilter = ProductFilter::where('slug', 'availability')->first();
        if ($availabilityFilter) {
            $status = $this->isInStock() ? 'in_stock' : 'out_of_stock';
            $displayValue = $this->isInStock() ? 'موجود' : 'ناموجود';
            $this->addFilterValue($availabilityFilter, $status, $displayValue);
        }
    }
}
