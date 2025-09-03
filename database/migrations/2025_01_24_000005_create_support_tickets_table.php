<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            
            // Ticket Details
            $table->string('subject');
            $table->text('description');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['open', 'in_progress', 'waiting_customer', 'waiting_admin', 'resolved', 'closed'])->default('open');
            $table->enum('category', ['general', 'technical', 'billing', 'product', 'shipping', 'refund', 'other'])->default('general');
            
            // Classification
            $table->json('tags')->nullable();
            $table->string('department')->nullable();
            
            // Related Information
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            
            // Timing
            $table->timestamp('first_response_at')->nullable();
            $table->timestamp('last_response_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            
            // Metrics
            $table->integer('response_time_minutes')->nullable(); // Time to first response
            $table->integer('resolution_time_minutes')->nullable(); // Time to resolution
            $table->integer('messages_count')->default(0);
            
            // Customer Satisfaction
            $table->integer('satisfaction_rating')->nullable(); // 1-5 stars
            $table->text('satisfaction_feedback')->nullable();
            
            // Internal Notes
            $table->text('internal_notes')->nullable();
            $table->boolean('is_escalated')->default(false);
            $table->timestamp('escalated_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['assigned_to', 'status']);
            $table->index(['status', 'priority']);
            $table->index(['category', 'status']);
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('support_tickets');
    }
};