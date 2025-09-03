<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            
            // Basic Info
            $table->string('sku')->unique()->nullable();
            $table->string('name')->nullable();
            
            // Pricing
            $table->decimal('price', 12, 2);
            $table->decimal('original_price', 12, 2)->nullable();
            
            // Inventory
            $table->integer('stock')->default(0);
            $table->decimal('weight', 8, 2)->nullable();
            $table->json('dimensions')->nullable(); // {length, width, height}
            
            // Variant Attributes
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->string('material')->nullable();
            $table->string('brand')->nullable();
            
            // Status & Settings
            $table->enum('status', ['active', 'inactive', 'discontinued'])->default('active');
            $table->boolean('is_default')->default(false);
            $table->integer('sort_order')->default(0);
            
            // Additional Attributes (JSON)
            $table->json('attributes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['product_id', 'status']);
            $table->index(['product_id', 'is_default']);
            $table->index(['color', 'size']);
            $table->index('stock');
            $table->index('price');
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_variants');
    }
};