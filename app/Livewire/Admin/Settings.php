<?php

namespace App\Livewire\Admin;

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
        // Load settings from cache or database
        $settings = Cache::get('site_settings', []);
        
        $this->site_name = $settings['site_name'] ?? config('app.name', 'فروشگاه کارین');
        $this->site_description = $settings['site_description'] ?? '';
        $this->contact_email = $settings['contact_email'] ?? '';
        $this->contact_phone = $settings['contact_phone'] ?? '';
        $this->site_address = $settings['site_address'] ?? '';
        
        $this->zarinpal_merchant_id = $settings['zarinpal_merchant_id'] ?? '';
        $this->mellat_terminal_id = $settings['mellat_terminal_id'] ?? '';
        $this->mellat_username = $settings['mellat_username'] ?? '';
        $this->mellat_password = $settings['mellat_password'] ?? '';
        
        $this->sms_api_key = $settings['sms_api_key'] ?? '';
        $this->sms_sender_number = $settings['sms_sender_number'] ?? '';
        
        $this->smtp_host = $settings['smtp_host'] ?? '';
        $this->smtp_port = $settings['smtp_port'] ?? '587';
        $this->smtp_username = $settings['smtp_username'] ?? '';
        $this->smtp_password = $settings['smtp_password'] ?? '';
        
        $this->currency = $settings['currency'] ?? 'تومان';
        $this->shipping_cost = $settings['shipping_cost'] ?? '0';
        $this->free_shipping_threshold = $settings['free_shipping_threshold'] ?? '0';
        $this->max_order_items = $settings['max_order_items'] ?? '10';
    }

    public function save()
    {
        $this->validate();

        $settings = [
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
        ];

        // Save to cache
        Cache::put('site_settings', $settings, now()->addDays(30));

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
