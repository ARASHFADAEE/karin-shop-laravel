<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('coupons', function (Blueprint $table) {
            // Advanced Coupon Features
            $table->decimal('maximum_discount', 12, 2)->nullable()->after('value');
            $table->integer('usage_limit_per_user')->nullable()->after('usage_limit');
            $table->timestamp('starts_at')->nullable()->after('expires_at');
            
            // Coupon Conditions
            $table->json('applicable_products')->nullable()->after('is_active'); // specific product IDs
            $table->json('applicable_categories')->nullable()->after('applicable_products'); // specific category IDs
            $table->json('excluded_products')->nullable()->after('applicable_categories'); // excluded product IDs
            $table->json('excluded_categories')->nullable()->after('excluded_products'); // excluded category IDs
            
            // User Restrictions
            $table->json('applicable_users')->nullable()->after('excluded_categories'); // specific user IDs
            $table->json('excluded_users')->nullable()->after('applicable_users'); // excluded user IDs
            $table->boolean('first_order_only')->default(false)->after('excluded_users');
            $table->integer('minimum_order_count')->nullable()->after('first_order_only');
            
            // Geographic Restrictions
            $table->json('applicable_cities')->nullable()->after('minimum_order_count');
            $table->json('excluded_cities')->nullable()->after('applicable_cities');
            
            // Combination Rules
            $table->boolean('combinable_with_other_coupons')->default(true)->after('excluded_cities');
            $table->boolean('combinable_with_product_discounts')->default(true)->after('combinable_with_other_coupons');
            
            // Auto Apply
            $table->boolean('auto_apply')->default(false)->after('combinable_with_product_discounts');
            $table->integer('priority')->default(0)->after('auto_apply'); // for auto-apply ordering
            
            // Referral System
            $table->boolean('is_referral_coupon')->default(false)->after('priority');
            $table->foreignId('referrer_user_id')->nullable()->constrained('users')->onDelete('set null')->after('is_referral_coupon');
            $table->decimal('referrer_reward', 12, 2)->nullable()->after('referrer_user_id');
            
            // Bulk Generation
            $table->string('generation_batch')->nullable()->after('referrer_reward');
            $table->boolean('is_single_use')->default(false)->after('generation_batch');
            
            // Analytics
            $table->decimal('total_discount_given', 12, 2)->default(0)->after('is_single_use');
            $table->integer('total_orders')->default(0)->after('total_discount_given');
            $table->timestamp('last_used_at')->nullable()->after('total_orders');
            
            // Notification Settings
            $table->boolean('notify_on_use')->default(false)->after('last_used_at');
            $table->boolean('notify_on_expiry')->default(false)->after('notify_on_use');
        });
    }

    public function down()
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn([
                'maximum_discount',
                'usage_limit_per_user',
                'starts_at',
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
            ]);
        });
    }
};