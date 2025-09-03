<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            
            // General Settings
            $table->string('site_name')->default('کارین شاپ');
            $table->text('site_description')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->text('site_address')->nullable();
            
            // Payment Settings
            $table->string('zarinpal_merchant_id')->nullable();
            $table->string('mellat_terminal_id')->nullable();
            $table->string('mellat_username')->nullable();
            $table->string('mellat_password')->nullable();
            
            // SMS Settings
            $table->string('sms_api_key')->nullable();
            $table->string('sms_sender_number')->nullable();
            
            // Melli Payamak SMS Settings
            $table->string('melli_payamak_username')->nullable();
            $table->string('melli_payamak_password')->nullable();
            $table->string('melli_payamak_sender_number')->nullable();
            
            // SMS Pattern Settings
            $table->string('sms_pattern_login_code')->nullable();
            $table->string('sms_pattern_order_created')->nullable();
            $table->string('sms_pattern_order_processing')->nullable();
            $table->string('sms_pattern_order_shipped')->nullable();
            $table->string('sms_pattern_order_delivered')->nullable();
            $table->string('sms_pattern_order_cancelled')->nullable();
            $table->string('sms_pattern_admin_new_order')->nullable();
            $table->string('sms_pattern_admin_low_stock')->nullable();
            
            // Email Settings
            $table->string('smtp_host')->nullable();
            $table->string('smtp_port')->nullable();
            $table->string('smtp_username')->nullable();
            $table->string('smtp_password')->nullable();
            
            // Shop Settings
            $table->string('currency')->default('تومان');
            $table->decimal('shipping_cost', 10, 2)->default(0);
            
            // Invoice Company Information
            $table->string('invoice_company_name')->default('کارین شاپ');
            $table->string('invoice_company_phone')->default('۰۹۱۴۰۰۶۳۷۹');
            $table->string('invoice_company_email')->default('info@karinshop.com');
            $table->text('invoice_company_address')->nullable();
            $table->string('invoice_company_website')->nullable();
            
            // Invoice Design Settings
            $table->string('invoice_logo_url')->nullable();
            $table->string('invoice_primary_color')->default('#2563eb');
            $table->string('invoice_secondary_color')->default('#1e40af');
            
            // Invoice Footer Settings
            $table->string('invoice_footer_text')->default('با تشکر از خرید شما');
            $table->text('invoice_terms')->nullable();
            $table->boolean('invoice_show_watermark')->default(true);
            $table->string('invoice_watermark_text')->default('کارین شاپ');
            
            $table->timestamps();
        });
        
        // Insert default settings
        DB::table('settings')->insert([
            'site_name' => 'کارین شاپ',
            'contact_phone' => '۰۹۱۴۰۰۶۳۷۹',
            'contact_email' => 'info@karinshop.com',
            'currency' => 'تومان',
            'shipping_cost' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('settings');
    }
};