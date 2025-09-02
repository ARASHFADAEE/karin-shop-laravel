<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('original_price', 12, 2)->nullable()->after('price');
            $table->decimal('discount_percentage', 5, 2)->nullable()->after('original_price');
            $table->decimal('discount_amount', 12, 2)->nullable()->after('discount_percentage');
            $table->boolean('has_discount')->default(false)->after('discount_amount');
            $table->timestamp('discount_starts_at')->nullable()->after('has_discount');
            $table->timestamp('discount_ends_at')->nullable()->after('discount_starts_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'original_price',
                'discount_percentage', 
                'discount_amount',
                'has_discount',
                'discount_starts_at',
                'discount_ends_at'
            ]);
        });
    }
};
