<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Settings extends Component
{
    // General Settings
    #[Rule('required|string|max:100')]
    public string $site_name = '';

    #[Rule('nullable|string|max:500')]
    public string $site_description = '';

    #[Rule('nullable|email|max:100')]
    public string $contact_email = '';

    #[Rule('nullable|string|max:20')]
    public string $contact_phone = '';

    #[Rule('nullable|string')]
    public string $site_address = '';

    // Payment Settings
    #[Rule('nullable|string|max:100')]
    public string $zarinpal_merchant_id = '';

    #[Rule('nullable|string|max:100')]
    public string $mellat_terminal_id = '';

    #[Rule('nullable|string|max:100')]
    public string $mellat_username = '';

    #[Rule('nullable|string|max:100')]
    public string $mellat_password = '';

    // SMS Settings
    #[Rule('nullable|string|max:100')]
    public string $sms_api_key = '';

    #[Rule('nullable|string|max:20')]
    public string $sms_sender_number = '';

    // Email Settings
    #[Rule('nullable|string|max:100')]
    public string $smtp_host = '';

    #[Rule('nullable|integer|min:1|max:65535')]
    public string $smtp_port = '';

    #[Rule('nullable|string|max:100')]
    public string $smtp_username = '';

    #[Rule('nullable|string|max:100')]
    public string $smtp_password = '';

    // Shop Settings
    #[Rule('required|string|max:10')]
    public string $currency = 'تومان';

    #[Rule('required|numeric|min:0')]
    public string $shipping_cost = '0';
    
    // Invoice Settings
    #[Rule('required|string|max:100')]
    public string $invoice_company_name = 'کارین شاپ';
    
    #[Rule('required|string|max:20')]
    public string $invoice_company_phone = '۰۹۱۴۰۰۶۳۷۹';
    
    #[Rule('required|email|max:100')]
    public string $invoice_company_email = 'info@karinshop.com';
    
    #[Rule('nullable|string')]
    public string $invoice_company_address = '';
    
    #[Rule('nullable|url|max:255')]
    public string $invoice_company_website = '';
    
    #[Rule('nullable|url|max:255')]
    public string $invoice_logo_url = '';
    
    #[Rule('required|string|max:7')]
    public string $invoice_primary_color = '#2563eb';
    
    #[Rule('required|string|max:7')]
    public string $invoice_secondary_color = '#1e40af';
    
    #[Rule('required|string|max:255')]
    public string $invoice_footer_text = 'با تشکر از خرید شما';
    
    #[Rule('nullable|string')]
    public string $invoice_terms = '';
    
    #[Rule('boolean')]
    public bool $invoice_show_watermark = true;
    
    #[Rule('required|string|max:50')]
    public string $invoice_watermark_text = 'کارین شاپ';
    
    // Melli Payamak SMS Settings
    #[Rule('nullable|string|max:100')]
    public string $melli_payamak_username = '';
    
    #[Rule('nullable|string|max:100')]
    public string $melli_payamak_password = '';
    
    #[Rule('nullable|string|max:20')]
    public string $melli_payamak_sender_number = '';
    
    // SMS Pattern Settings
    #[Rule('nullable|string|max:255')]
    public string $sms_pattern_login_code = '';
    
    #[Rule('nullable|string|max:255')]
    public string $sms_pattern_order_created = '';
    
    #[Rule('nullable|string|max:255')]
    public string $sms_pattern_order_processing = '';
    
    #[Rule('nullable|string|max:255')]
    public string $sms_pattern_order_shipped = '';
    
    #[Rule('nullable|string|max:255')]
    public string $sms_pattern_order_delivered = '';
    
    #[Rule('nullable|string|max:255')]
    public string $sms_pattern_order_cancelled = '';
    
    #[Rule('nullable|string|max:255')]
    public string $sms_pattern_admin_new_order = '';
    
    #[Rule('nullable|string|max:255')]
    public string $sms_pattern_admin_low_stock = '';

    #[Rule('required|numeric|min:0')]
    public string $free_shipping_threshold = '0';

    #[Rule('required|integer|min:1')]
    public string $max_order_items = '10';

    public function mount()
    {
        $this->loadSettings();
    }

    public function loadSettings()
    {
        // Load settings from database
        $setting = Setting::first();
        
        $this->site_name = $setting->site_name ?? config('app.name', 'فروشگاه کارین');
        $this->site_description = $setting->site_description ?? '';
        $this->contact_email = $setting->contact_email ?? '';
        $this->contact_phone = $setting->contact_phone ?? '';
        $this->site_address = $setting->site_address ?? '';
        
        $this->zarinpal_merchant_id = $setting->zarinpal_merchant_id ?? '';
        $this->mellat_terminal_id = $setting->mellat_terminal_id ?? '';
        $this->mellat_username = $setting->mellat_username ?? '';
        $this->mellat_password = $setting->mellat_password ?? '';
        
        $this->sms_api_key = $setting->sms_api_key ?? '';
        $this->sms_sender_number = $setting->sms_sender_number ?? '';
        
        $this->smtp_host = $setting->smtp_host ?? '';
        $this->smtp_port = $setting->smtp_port ?? '587';
        $this->smtp_username = $setting->smtp_username ?? '';
        $this->smtp_password = $setting->smtp_password ?? '';
        
        $this->currency = $setting->currency ?? 'تومان';
        $this->shipping_cost = $setting->shipping_cost ?? '0';
        $this->free_shipping_threshold = $setting->free_shipping_threshold ?? '0';
        $this->max_order_items = $setting->max_order_items ?? '10';
        
        // Invoice Settings
        $this->invoice_company_name = $setting->invoice_company_name ?? 'کارین شاپ';
        $this->invoice_company_phone = $setting->invoice_company_phone ?? '۰۹۱۴۰۰۶۳۷۹';
        $this->invoice_company_email = $setting->invoice_company_email ?? 'info@karinshop.com';
        $this->invoice_company_address = $setting->invoice_company_address ?? '';
        $this->invoice_company_website = $setting->invoice_company_website ?? '';
        $this->invoice_logo_url = $setting->invoice_logo_url ?? '';
        $this->invoice_primary_color = $setting->invoice_primary_color ?? '#2563eb';
        $this->invoice_secondary_color = $setting->invoice_secondary_color ?? '#1e40af';
        $this->invoice_footer_text = $setting->invoice_footer_text ?? 'با تشکر از خرید شما';
        $this->invoice_terms = $setting->invoice_terms ?? '';
        $this->invoice_show_watermark = $setting->invoice_show_watermark ?? true;
        $this->invoice_watermark_text = $setting->invoice_watermark_text ?? 'کارین شاپ';
        
        // Melli Payamak SMS Settings
        $this->melli_payamak_username = $setting->melli_payamak_username ?? '';
        $this->melli_payamak_password = $setting->melli_payamak_password ?? '';
        $this->melli_payamak_sender_number = $setting->melli_payamak_sender_number ?? '';
        
        // SMS Pattern Settings
        $this->sms_pattern_login_code = $setting->sms_pattern_login_code ?? '';
        $this->sms_pattern_order_created = $setting->sms_pattern_order_created ?? '';
        $this->sms_pattern_order_processing = $setting->sms_pattern_order_processing ?? '';
        $this->sms_pattern_order_shipped = $setting->sms_pattern_order_shipped ?? '';
        $this->sms_pattern_order_delivered = $setting->sms_pattern_order_delivered ?? '';
        $this->sms_pattern_order_cancelled = $setting->sms_pattern_order_cancelled ?? '';
        $this->sms_pattern_admin_new_order = $setting->sms_pattern_admin_new_order ?? '';
        $this->sms_pattern_admin_low_stock = $setting->sms_pattern_admin_low_stock ?? '';
    }

    public function save()
    {
        $this->validate();

        $data = [
            'site_name' => $this->site_name,
            'site_description' => $this->site_description,
            'contact_email' => $this->contact_email,
            'contact_phone' => $this->contact_phone,
            'site_address' => $this->site_address,
            'zarinpal_merchant_id' => $this->zarinpal_merchant_id,
            'mellat_terminal_id' => $this->mellat_terminal_id,
            'mellat_username' => $this->mellat_username,
            'mellat_password' => $this->mellat_password,
            'sms_api_key' => $this->sms_api_key,
            'sms_sender_number' => $this->sms_sender_number,
            'smtp_host' => $this->smtp_host,
            'smtp_port' => $this->smtp_port,
            'smtp_username' => $this->smtp_username,
            'smtp_password' => $this->smtp_password,
            'currency' => $this->currency,
            'shipping_cost' => $this->shipping_cost,
            'free_shipping_threshold' => $this->free_shipping_threshold,
            'max_order_items' => $this->max_order_items,
            
            // Invoice Settings
            'invoice_company_name' => $this->invoice_company_name,
            'invoice_company_phone' => $this->invoice_company_phone,
            'invoice_company_email' => $this->invoice_company_email,
            'invoice_company_address' => $this->invoice_company_address,
            'invoice_company_website' => $this->invoice_company_website,
            'invoice_logo_url' => $this->invoice_logo_url,
            'invoice_primary_color' => $this->invoice_primary_color,
            'invoice_secondary_color' => $this->invoice_secondary_color,
            'invoice_footer_text' => $this->invoice_footer_text,
            'invoice_terms' => $this->invoice_terms,
            'invoice_show_watermark' => $this->invoice_show_watermark,
            'invoice_watermark_text' => $this->invoice_watermark_text,
            
            // Melli Payamak SMS Settings
            'melli_payamak_username' => $this->melli_payamak_username,
            'melli_payamak_password' => $this->melli_payamak_password,
            'melli_payamak_sender_number' => $this->melli_payamak_sender_number,
            
            // SMS Pattern Settings
            'sms_pattern_login_code' => $this->sms_pattern_login_code,
            'sms_pattern_order_created' => $this->sms_pattern_order_created,
            'sms_pattern_order_processing' => $this->sms_pattern_order_processing,
            'sms_pattern_order_shipped' => $this->sms_pattern_order_shipped,
            'sms_pattern_order_delivered' => $this->sms_pattern_order_delivered,
            'sms_pattern_order_cancelled' => $this->sms_pattern_order_cancelled,
            'sms_pattern_admin_new_order' => $this->sms_pattern_admin_new_order,
            'sms_pattern_admin_low_stock' => $this->sms_pattern_admin_low_stock,
        ];

        // Save to database
        Setting::updateOrCreate([], $data);

        session()->flash('success', 'تنظیمات با موفقیت ذخیره شد.');
    }

    public function clearCache()
    {
        Cache::forget('site_settings');
        $this->loadSettings();
        session()->flash('success', 'کش تنظیمات پاک شد.');
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.admin.settings');
    }
}
