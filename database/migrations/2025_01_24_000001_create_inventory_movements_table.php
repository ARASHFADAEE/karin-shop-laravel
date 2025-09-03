<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            
            // Movement Details
            $table->enum('type', ['in', 'out', 'adjustment', 'transfer', 'return', 'damage', 'expired']);
            $table->integer('quantity');
            $table->integer('previous_quantity');
            $table->integer('new_quantity');
            
            // Reference Information
            $table->string('reference_type')->nullable(); // order, purchase, adjustment, etc.
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_number')->nullable();
            
            // Movement Details
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->decimal('total_cost', 10, 2)->nullable();
            
            // Location Information
            $table->string('from_location')->nullable();
            $table->string('to_location')->nullable();
            
            // Batch/Lot Information
            $table->string('batch_number')->nullable();
            $table->date('expiry_date')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['product_id', 'created_at']);
            $table->index(['type', 'created_at']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory_movements');
    }
};