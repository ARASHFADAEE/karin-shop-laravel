<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_filter_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('filter_id')->constrained('product_filters')->onDelete('cascade');
            $table->string('value'); // مقدار فیلتر
            $table->string('display_value')->nullable(); // نمایش فارسی
            $table->decimal('numeric_value', 10, 2)->nullable(); // برای فیلترهای عددی
            $table->timestamps();
            
            $table->unique(['product_id', 'filter_id', 'value']);
            $table->index(['filter_id', 'value']);
            $table->index(['product_id', 'filter_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_filter_values');
    }
};