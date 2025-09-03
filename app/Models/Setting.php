<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        // General Settings
        'site_name',
        'site_description',
        'contact_email',
        'contact_phone',
        'site_address',
        
        // Payment Settings
        'zarinpal_merchant_id',
        'mellat_terminal_id',
        'mellat_username',
        'mellat_password',
        
        // SMS Settings
        'sms_api_key',
        'sms_sender_number',
        
        // Melli Payamak SMS Settings
        'melli_payamak_username',
        'melli_payamak_password',
        'melli_payamak_sender_number',
        
        // SMS Pattern Settings
        'sms_pattern_login_code',
        'sms_pattern_order_created',
        'sms_pattern_order_processing',
        'sms_pattern_order_shipped',
        'sms_pattern_order_delivered',
        'sms_pattern_order_cancelled',
        'sms_pattern_admin_new_order',
        'sms_pattern_admin_low_stock',
        
        // Email Settings
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        
        // Shop Settings
        'currency',
        'shipping_cost',
        
        // Invoice Settings
        'invoice_company_name',
        'invoice_company_phone',
        'invoice_company_email',
        'invoice_company_address',
        'invoice_company_website',
        'invoice_logo_url',
        'invoice_primary_color',
        'invoice_secondary_color',
        'invoice_footer_text',
        'invoice_terms',
        'invoice_show_watermark',
        'invoice_watermark_text',
    ];

    protected $casts = [
        'invoice_show_watermark' => 'boolean',
        'shipping_cost' => 'decimal:2',
    ];

    /**
     * Get a setting value by key
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::first();
            return $setting ? $setting->{$key} ?? $default : $default;
        });
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, $value): void
    {
        $setting = static::firstOrCreate([]);
        $setting->update([$key => $value]);
        
        // Clear cache
        Cache::forget("setting_{$key}");
    }

    /**
     * Get all settings as array
     */
    public static function getAll(): array
    {
        return Cache::remember('all_settings', 3600, function () {
            $setting = static::first();
            return $setting ? $setting->toArray() : [];
        });
    }

    /**
     * Clear all settings cache
     */
    public static function clearCache(): void
    {
        Cache::forget('all_settings');
        
        $setting = static::first();
        if ($setting) {
            foreach ($setting->getFillable() as $key) {
                Cache::forget("setting_{$key}");
            }
        }
    }

    /**
     * Boot method to clear cache on model events
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            static::clearCache();
        });

        static::deleted(function () {
            static::clearCache();
        });
    }
}