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
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'has_discount' => 'boolean',
        'discount_starts_at' => 'datetime',
        'discount_ends_at' => 'datetime',
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
}
