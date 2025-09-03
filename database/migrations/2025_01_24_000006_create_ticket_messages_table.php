<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ticket_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('support_tickets')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Message Content
            $table->text('message');
            $table->json('attachments')->nullable(); // File attachments
            
            // Message Type
            $table->enum('type', ['message', 'note', 'status_change', 'assignment', 'escalation'])->default('message');
            $table->boolean('is_internal')->default(false); // Internal admin notes
            
            // Status Tracking
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            
            // Auto-generated messages
            $table->boolean('is_system_generated')->default(false);
            $table->json('system_data')->nullable(); // For system-generated messages
            
            $table->timestamps();
            
            // Indexes
            $table->index(['ticket_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['is_internal', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('ticket_messages');
    }
};