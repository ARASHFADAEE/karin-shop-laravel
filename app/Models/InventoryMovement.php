<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'quantity',
        'previous_quantity',
        'new_quantity',
        'reference_type',
        'reference_id',
        'reference_number',
        'reason',
        'notes',
        'unit_cost',
        'total_cost',
        'from_location',
        'to_location',
        'batch_number',
        'expiry_date'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    // Movement types
    const TYPE_IN = 'in';
    const TYPE_OUT = 'out';
    const TYPE_ADJUSTMENT = 'adjustment';
    const TYPE_TRANSFER = 'transfer';
    const TYPE_RETURN = 'return';
    const TYPE_DAMAGE = 'damage';
    const TYPE_EXPIRED = 'expired';

    /**
     * Get the product that owns the movement
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who created the movement
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the reference model (polymorphic)
     */
    public function reference()
    {
        return $this->morphTo('reference', 'reference_type', 'reference_id');
    }

    /**
     * Scope for filtering by movement type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get movement type label
     */
    public function getTypeLabel(): string
    {
        return match($this->type) {
            self::TYPE_IN => 'ورود کالا',
            self::TYPE_OUT => 'خروج کالا',
            self::TYPE_ADJUSTMENT => 'تعدیل موجودی',
            self::TYPE_TRANSFER => 'انتقال',
            self::TYPE_RETURN => 'برگشت',
            self::TYPE_DAMAGE => 'آسیب دیده',
            self::TYPE_EXPIRED => 'منقضی شده',
            default => $this->type
        };
    }

    /**
     * Get movement direction (positive or negative)
     */
    public function getDirection(): string
    {
        return in_array($this->type, [self::TYPE_IN, self::TYPE_RETURN]) ? 'positive' : 'negative';
    }

    /**
     * Create a new inventory movement
     */
    public static function createMovement(
        Product $product,
        string $type,
        int $quantity,
        ?User $user = null,
        array $additionalData = []
    ): self {
        $previousQuantity = $product->stock_quantity ?? 0;
        
        // Calculate new quantity based on movement type
        $newQuantity = match($type) {
            self::TYPE_IN, self::TYPE_RETURN => $previousQuantity + $quantity,
            self::TYPE_OUT, self::TYPE_DAMAGE, self::TYPE_EXPIRED => $previousQuantity - $quantity,
            self::TYPE_ADJUSTMENT => $quantity, // For adjustments, quantity is the new total
            self::TYPE_TRANSFER => $previousQuantity, // Transfers don't change total quantity
            default => $previousQuantity
        };

        // Create the movement record
        $movement = self::create(array_merge([
            'product_id' => $product->id,
            'user_id' => $user?->id,
            'type' => $type,
            'quantity' => $type === self::TYPE_ADJUSTMENT ? $quantity - $previousQuantity : $quantity,
            'previous_quantity' => $previousQuantity,
            'new_quantity' => $newQuantity,
        ], $additionalData));

        // Update product stock quantity
        if ($type !== self::TYPE_TRANSFER) {
            $product->update(['stock_quantity' => $newQuantity]);
            $product->updateStockStatus();
        }

        return $movement;
    }
}