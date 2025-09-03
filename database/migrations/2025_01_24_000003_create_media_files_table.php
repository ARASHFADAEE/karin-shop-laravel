<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('media_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // File Information
            $table->string('name');
            $table->string('original_name');
            $table->string('file_path');
            $table->string('file_url');
            $table->string('disk')->default('public');
            
            // File Details
            $table->string('mime_type');
            $table->string('extension');
            $table->unsignedBigInteger('size'); // in bytes
            $table->json('metadata')->nullable(); // width, height, duration, etc.
            
            // File Type
            $table->enum('type', ['image', 'video', 'audio', 'document', 'archive', 'other']);
            
            // Organization
            $table->string('folder')->nullable();
            $table->json('tags')->nullable();
            $table->text('description')->nullable();
            $table->string('alt_text')->nullable(); // for images
            
            // Usage Tracking
            $table->boolean('is_public')->default(false);
            $table->integer('download_count')->default(0);
            $table->timestamp('last_accessed_at')->nullable();
            
            // Image Specific
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->json('thumbnails')->nullable(); // different sizes
            
            // Status
            $table->enum('status', ['active', 'archived', 'deleted'])->default('active');
            
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'type']);
            $table->index(['folder', 'type']);
            $table->index(['mime_type']);
            $table->index(['status', 'created_at']);
            $table->fullText(['name', 'original_name', 'description']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('media_files');
    }
};