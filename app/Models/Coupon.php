<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'description',
        'type',
        'value',
        'maximum_discount',
        'minimum_amount',
        'usage_limit',
        'usage_limit_per_user',
        'usage_count',
        'starts_at',
        'expires_at',
        'is_active',
        'applicable_products',
        'applicable_categories',
        'excluded_products',
        'excluded_categories',
        'applicable_users',
        'excluded_users',
        'first_order_only',
        'minimum_order_count',
        'applicable_cities',
        'excluded_cities',
        'combinable_with_other_coupons',
        'combinable_with_product_discounts',
        'auto_apply',
        'priority',
        'is_referral_coupon',
        'referrer_user_id',
        'referrer_reward',
        'generation_batch',
        'is_single_use',
        'total_discount_given',
        'total_orders',
        'last_used_at',
        'notify_on_use',
        'notify_on_expiry'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'referrer_reward' => 'decimal:2',
        'total_discount_given' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
        'is_active' => 'boolean',
        'applicable_products' => 'array',
        'applicable_categories' => 'array',
        'excluded_products' => 'array',
        'excluded_categories' => 'array',
        'applicable_users' => 'array',
        'excluded_users' => 'array',
        'applicable_cities' => 'array',
        'excluded_cities' => 'array',
        'first_order_only' => 'boolean',
        'combinable_with_other_coupons' => 'boolean',
        'combinable_with_product_discounts' => 'boolean',
        'auto_apply' => 'boolean',
        'is_referral_coupon' => 'boolean',
        'is_single_use' => 'boolean',
        'notify_on_use' => 'boolean',
        'notify_on_expiry' => 'boolean',
    ];

    // Coupon types
    const TYPE_PERCENTAGE = 'percentage';
    const TYPE_FIXED = 'fixed';

    // Relationships
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'coupon_user')->withPivot('used_at')->withTimestamps();
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_user_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        return $query->active()
                    ->where(function($q) {
                        $q->whereNull('starts_at')
                          ->orWhere('starts_at', '<=', now());
                    })
                    ->where(function($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>=', now());
                    });
    }

    public function scopeAutoApply($query)
    {
        return $query->where('auto_apply', true)->orderBy('priority', 'desc');
    }

    public function scopeForUser($query, User $user)
    {
        return $query->where(function($q) use ($user) {
            $q->whereNull('applicable_users')
              ->orWhereJsonContains('applicable_users', $user->id);
        })->where(function($q) use ($user) {
            $q->whereNull('excluded_users')
              ->orWhereJsonDoesntContain('excluded_users', $user->id);
        });
    }

    // Validation Methods
    public function isValidForUser(User $user, $cartItems = [], $orderTotal = 0): array
    {
        $errors = [];

        // Check if coupon is active
        if (!$this->is_active) {
            $errors[] = 'این کوپن غیرفعال است.';
        }

        // Check date validity
        if ($this->starts_at && $this->starts_at->isFuture()) {
            $errors[] = 'این کوپن هنوز فعال نشده است.';
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            $errors[] = 'این کوپن منقضی شده است.';
        }

        // Check usage limits
        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            $errors[] = 'حد استفاده از این کوپن تمام شده است.';
        }

        // Check per-user usage limit
        if ($this->usage_limit_per_user) {
            $userUsageCount = $this->users()->where('user_id', $user->id)->count();
            if ($userUsageCount >= $this->usage_limit_per_user) {
                $errors[] = 'شما قبلاً از این کوپن استفاده کرده‌اید.';
            }
        }

        // Check single use
        if ($this->is_single_use && $this->usage_count > 0) {
            $errors[] = 'این کوپن یکبار مصرف است و قبلاً استفاده شده.';
        }

        // Check minimum amount
        if ($this->minimum_amount && $orderTotal < $this->minimum_amount) {
            $errors[] = "حداقل مبلغ سفارش برای استفاده از این کوپن {$this->minimum_amount} تومان است.";
        }

        // Check user restrictions
        if ($this->applicable_users && !in_array($user->id, $this->applicable_users)) {
            $errors[] = 'این کوپن برای شما قابل استفاده نیست.';
        }

        if ($this->excluded_users && in_array($user->id, $this->excluded_users)) {
            $errors[] = 'شما از استفاده از این کوپن محروم هستید.';
        }

        // Check first order only
        if ($this->first_order_only) {
            $orderCount = Order::where('user_id', $user->id)->count();
            if ($orderCount > 0) {
                $errors[] = 'این کوپن فقط برای اولین سفارش قابل استفاده است.';
            }
        }

        // Check minimum order count
        if ($this->minimum_order_count) {
            $orderCount = Order::where('user_id', $user->id)->count();
            if ($orderCount < $this->minimum_order_count) {
                $errors[] = "برای استفاده از این کوپن باید حداقل {$this->minimum_order_count} سفارش داشته باشید.";
            }
        }

        // Check product/category restrictions
        if ($cartItems && ($this->applicable_products || $this->applicable_categories || $this->excluded_products || $this->excluded_categories)) {
            $validItems = $this->getValidCartItems($cartItems);
            if (empty($validItems)) {
                $errors[] = 'این کوپن برای محصولات موجود در سبد خرید شما قابل استفاده نیست.';
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    public function getValidCartItems($cartItems): array
    {
        $validItems = [];

        foreach ($cartItems as $item) {
            $product = $item['product'] ?? $item;
            $productId = is_object($product) ? $product->id : $product['id'];
            $categoryIds = is_object($product) ? $product->categories->pluck('id')->toArray() : ($product['category_ids'] ?? []);

            $isValid = true;

            // Check applicable products
            if ($this->applicable_products && !in_array($productId, $this->applicable_products)) {
                $isValid = false;
            }

            // Check applicable categories
            if ($this->applicable_categories && !array_intersect($categoryIds, $this->applicable_categories)) {
                $isValid = false;
            }

            // Check excluded products
            if ($this->excluded_products && in_array($productId, $this->excluded_products)) {
                $isValid = false;
            }

            // Check excluded categories
            if ($this->excluded_categories && array_intersect($categoryIds, $this->excluded_categories)) {
                $isValid = false;
            }

            if ($isValid) {
                $validItems[] = $item;
            }
        }

        return $validItems;
    }

    // Discount Calculation
    public function calculateDiscount($orderTotal, $cartItems = []): float
    {
        if ($this->applicable_products || $this->applicable_categories || $this->excluded_products || $this->excluded_categories) {
            // Calculate discount only for valid items
            $validItems = $this->getValidCartItems($cartItems);
            $validTotal = array_sum(array_map(function($item) {
                return ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
            }, $validItems));
            $orderTotal = $validTotal;
        }

        if ($this->type === self::TYPE_PERCENTAGE) {
            $discount = ($orderTotal * $this->value) / 100;
            
            // Apply maximum discount limit
            if ($this->maximum_discount && $discount > $this->maximum_discount) {
                $discount = $this->maximum_discount;
            }
        } else {
            $discount = min($this->value, $orderTotal);
        }

        return round($discount, 2);
    }

    // Usage Tracking
    public function markAsUsed(User $user, float $discountAmount, Order $order = null): void
    {
        // Attach user to coupon
        $this->users()->attach($user->id, [
            'used_at' => now(),
            'order_id' => $order?->id,
            'discount_amount' => $discountAmount
        ]);

        // Update coupon statistics
        $this->increment('usage_count');
        $this->increment('total_discount_given', $discountAmount);
        $this->increment('total_orders');
        $this->update(['last_used_at' => now()]);

        // Handle referral rewards
        if ($this->is_referral_coupon && $this->referrer && $this->referrer_reward) {
            // TODO: Implement referral reward logic
        }

        // Send notifications if enabled
        if ($this->notify_on_use) {
            // TODO: Send notification
        }
    }

    // Static Methods
    public static function findByCode(string $code): ?self
    {
        return self::where('code', $code)->first();
    }

    public static function getAutoApplicableCoupons(User $user, $cartItems = [], $orderTotal = 0): array
    {
        $coupons = self::valid()->autoApply()->forUser($user)->get();
        $applicableCoupons = [];

        foreach ($coupons as $coupon) {
            $validation = $coupon->isValidForUser($user, $cartItems, $orderTotal);
            if ($validation['valid']) {
                $applicableCoupons[] = [
                    'coupon' => $coupon,
                    'discount' => $coupon->calculateDiscount($orderTotal, $cartItems)
                ];
            }
        }

        // Sort by discount amount (highest first)
        usort($applicableCoupons, function($a, $b) {
            return $b['discount'] <=> $a['discount'];
        });

        return $applicableCoupons;
    }

    public static function generateBulkCoupons(array $config): array
    {
        $coupons = [];
        $batchId = uniqid('batch_');
        
        for ($i = 0; $i < $config['count']; $i++) {
            $code = $config['prefix'] . strtoupper(uniqid());
            
            $coupon = self::create(array_merge($config['coupon_data'], [
                'code' => $code,
                'generation_batch' => $batchId,
                'is_single_use' => true
            ]));
            
            $coupons[] = $coupon;
        }
        
        return $coupons;
    }

    // Utility Methods
    public function getTypeLabel(): string
    {
        return match($this->type) {
            self::TYPE_PERCENTAGE => 'درصدی',
            self::TYPE_FIXED => 'مبلغ ثابت',
            default => $this->type
        };
    }

    public function getDiscountText(): string
    {
        if ($this->type === self::TYPE_PERCENTAGE) {
            $text = "{$this->value}% تخفیف";
            if ($this->maximum_discount) {
                $text .= " (حداکثر {$this->maximum_discount} تومان)";
            }
        } else {
            $text = "{$this->value} تومان تخفیف";
        }
        
        return $text;
    }

    public function isExpiringSoon(int $days = 7): bool
    {
        return $this->expires_at && $this->expires_at->diffInDays(now()) <= $days;
    }

    public function getUsagePercentage(): float
    {
        if (!$this->usage_limit) {
            return 0;
        }
        
        return ($this->usage_count / $this->usage_limit) * 100;
    }
}
