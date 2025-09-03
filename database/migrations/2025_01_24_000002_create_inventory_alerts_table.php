<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inventory_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            
            // Alert Details
            $table->enum('type', ['low_stock', 'out_of_stock', 'reorder_point', 'expiry_warning', 'overstock']);
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->string('title');
            $table->text('message');
            
            // Alert Status
            $table->enum('status', ['active', 'acknowledged', 'resolved', 'dismissed'])->default('active');
            $table->timestamp('triggered_at');
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('acknowledged_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Alert Data
            $table->json('alert_data')->nullable(); // Store relevant data like current stock, threshold, etc.
            
            // Notification Settings
            $table->boolean('email_sent')->default(false);
            $table->boolean('sms_sent')->default(false);
            $table->timestamp('last_notification_sent')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['product_id', 'status']);
            $table->index(['type', 'status']);
            $table->index(['severity', 'status']);
            $table->index('triggered_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory_alerts');
    }
};