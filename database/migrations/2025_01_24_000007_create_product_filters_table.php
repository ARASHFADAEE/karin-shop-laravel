<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_filters', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // نام فیلتر (برند، رنگ، سایز، ...)
            $table->string('slug')->unique();
            $table->string('type'); // select, checkbox, range, color, size
            $table->text('description')->nullable();
            $table->json('options')->nullable(); // گزینه‌های فیلتر
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_filters');
    }
};