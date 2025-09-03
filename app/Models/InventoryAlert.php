<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Services\MelliPayamakService;

class InventoryAlert extends Model
{
    protected $fillable = [
        'product_id',
        'type',
        'severity',
        'title',
        'message',
        'status',
        'triggered_at',
        'acknowledged_at',
        'resolved_at',
        'acknowledged_by',
        'resolved_by',
        'alert_data',
        'email_sent',
        'sms_sent',
        'last_notification_sent'
    ];

    protected $casts = [
        'triggered_at' => 'datetime',
        'acknowledged_at' => 'datetime',
        'resolved_at' => 'datetime',
        'last_notification_sent' => 'datetime',
        'alert_data' => 'array',
        'email_sent' => 'boolean',
        'sms_sent' => 'boolean',
    ];

    // Alert types
    const TYPE_LOW_STOCK = 'low_stock';
    const TYPE_OUT_OF_STOCK = 'out_of_stock';
    const TYPE_REORDER_POINT = 'reorder_point';
    const TYPE_EXPIRY_WARNING = 'expiry_warning';
    const TYPE_OVERSTOCK = 'overstock';

    // Severity levels
    const SEVERITY_LOW = 'low';
    const SEVERITY_MEDIUM = 'medium';
    const SEVERITY_HIGH = 'high';
    const SEVERITY_CRITICAL = 'critical';

    // Status types
    const STATUS_ACTIVE = 'active';
    const STATUS_ACKNOWLEDGED = 'acknowledged';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_DISMISSED = 'dismissed';

    /**
     * Get the product that owns the alert
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who acknowledged the alert
     */
    public function acknowledgedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    /**
     * Get the user who resolved the alert
     */
    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * Scope for active alerts
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope for alerts by severity
     */
    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope for alerts by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get severity color for UI
     */
    public function getSeverityColor(): string
    {
        return match($this->severity) {
            self::SEVERITY_LOW => 'blue',
            self::SEVERITY_MEDIUM => 'yellow',
            self::SEVERITY_HIGH => 'orange',
            self::SEVERITY_CRITICAL => 'red',
            default => 'gray'
        };
    }

    /**
     * Get type label
     */
    public function getTypeLabel(): string
    {
        return match($this->type) {
            self::TYPE_LOW_STOCK => 'موجودی کم',
            self::TYPE_OUT_OF_STOCK => 'اتمام موجودی',
            self::TYPE_REORDER_POINT => 'نقطه سفارش مجدد',
            self::TYPE_EXPIRY_WARNING => 'هشدار انقضا',
            self::TYPE_OVERSTOCK => 'موجودی اضافی',
            default => $this->type
        };
    }

    /**
     * Acknowledge the alert
     */
    public function acknowledge(?User $user = null): bool
    {
        return $this->update([
            'status' => self::STATUS_ACKNOWLEDGED,
            'acknowledged_at' => now(),
            'acknowledged_by' => $user?->id
        ]);
    }

    /**
     * Resolve the alert
     */
    public function resolve(?User $user = null): bool
    {
        return $this->update([
            'status' => self::STATUS_RESOLVED,
            'resolved_at' => now(),
            'resolved_by' => $user?->id
        ]);
    }

    /**
     * Dismiss the alert
     */
    public function dismiss(): bool
    {
        return $this->update(['status' => self::STATUS_DISMISSED]);
    }

    /**
     * Send notification for this alert
     */
    public function sendNotification(): void
    {
        $settings = Setting::first();
        
        // Send SMS notification
        if ($settings && $settings->melli_payamak_username) {
            $smsService = new MelliPayamakService();
            $adminPhone = $settings->contact_phone;
            
            if ($adminPhone) {
                $message = "هشدار موجودی\n";
                $message .= "محصول: {$this->product->name}\n";
                $message .= "نوع هشدار: {$this->getTypeLabel()}\n";
                $message .= "موجودی فعلی: {$this->product->stock_quantity}";
                
                $result = $smsService->sendSMS($adminPhone, $message);
                
                if ($result['success']) {
                    $this->update([
                        'sms_sent' => true,
                        'last_notification_sent' => now()
                    ]);
                }
            }
        }
        
        // TODO: Add email notification
        $this->update(['email_sent' => true]);
    }

    /**
     * Create a new inventory alert
     */
    public static function createAlert(
        Product $product,
        string $type,
        string $severity,
        string $title,
        string $message,
        array $alertData = []
    ): self {
        // Check if similar alert already exists
        $existingAlert = self::where('product_id', $product->id)
            ->where('type', $type)
            ->where('status', self::STATUS_ACTIVE)
            ->first();

        if ($existingAlert) {
            // Update existing alert
            $existingAlert->update([
                'severity' => $severity,
                'title' => $title,
                'message' => $message,
                'alert_data' => $alertData,
                'triggered_at' => now()
            ]);
            return $existingAlert;
        }

        // Create new alert
        $alert = self::create([
            'product_id' => $product->id,
            'type' => $type,
            'severity' => $severity,
            'title' => $title,
            'message' => $message,
            'status' => self::STATUS_ACTIVE,
            'triggered_at' => now(),
            'alert_data' => $alertData
        ]);

        // Send notification
        $alert->sendNotification();

        return $alert;
    }

    /**
     * Check and create alerts for a product
     */
    public static function checkProductAlerts(Product $product): void
    {
        if (!$product->track_inventory) {
            return;
        }

        $currentStock = $product->stock_quantity ?? 0;
        $lowStockThreshold = $product->low_stock_threshold ?? 5;
        $reorderPoint = $product->reorder_point ?? 10;

        // Check for out of stock
        if ($currentStock <= 0) {
            self::createAlert(
                $product,
                self::TYPE_OUT_OF_STOCK,
                self::SEVERITY_CRITICAL,
                'اتمام موجودی',
                "موجودی محصول {$product->name} به اتمام رسیده است.",
                ['current_stock' => $currentStock]
            );
        }
        // Check for low stock
        elseif ($currentStock <= $lowStockThreshold) {
            self::createAlert(
                $product,
                self::TYPE_LOW_STOCK,
                self::SEVERITY_HIGH,
                'موجودی کم',
                "موجودی محصول {$product->name} کمتر از حد آستانه است. موجودی فعلی: {$currentStock}",
                ['current_stock' => $currentStock, 'threshold' => $lowStockThreshold]
            );
        }
        // Check for reorder point
        elseif ($currentStock <= $reorderPoint) {
            self::createAlert(
                $product,
                self::TYPE_REORDER_POINT,
                self::SEVERITY_MEDIUM,
                'نقطه سفارش مجدد',
                "موجودی محصول {$product->name} به نقطه سفارش مجدد رسیده است. موجودی فعلی: {$currentStock}",
                ['current_stock' => $currentStock, 'reorder_point' => $reorderPoint]
            );
        }
    }
}