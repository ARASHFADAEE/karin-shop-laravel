<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

class MediaFile extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'original_name',
        'file_path',
        'file_url',
        'disk',
        'mime_type',
        'extension',
        'size',
        'metadata',
        'type',
        'folder',
        'tags',
        'description',
        'alt_text',
        'is_public',
        'download_count',
        'last_accessed_at',
        'width',
        'height',
        'thumbnails',
        'status'
    ];

    protected $casts = [
        'metadata' => 'array',
        'tags' => 'array',
        'thumbnails' => 'array',
        'is_public' => 'boolean',
        'last_accessed_at' => 'datetime',
    ];

    // File types
    const TYPE_IMAGE = 'image';
    const TYPE_VIDEO = 'video';
    const TYPE_AUDIO = 'audio';
    const TYPE_DOCUMENT = 'document';
    const TYPE_ARCHIVE = 'archive';
    const TYPE_OTHER = 'other';

    // Status types
    const STATUS_ACTIVE = 'active';
    const STATUS_ARCHIVED = 'archived';
    const STATUS_DELETED = 'deleted';

    // Thumbnail sizes
    const THUMBNAIL_SIZES = [
        'small' => [150, 150],
        'medium' => [300, 300],
        'large' => [600, 600]
    ];

    /**
     * Get the user who uploaded the file
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for active files
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope for files by type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for images only
     */
    public function scopeImages($query)
    {
        return $query->where('type', self::TYPE_IMAGE);
    }

    /**
     * Scope for files in folder
     */
    public function scopeInFolder($query, $folder)
    {
        return $query->where('folder', $folder);
    }

    /**
     * Scope for public files
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Get file size in human readable format
     */
    public function getHumanSizeAttribute(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get file type icon
     */
    public function getTypeIcon(): string
    {
        return match($this->type) {
            self::TYPE_IMAGE => '🖼️',
            self::TYPE_VIDEO => '🎥',
            self::TYPE_AUDIO => '🎵',
            self::TYPE_DOCUMENT => '📄',
            self::TYPE_ARCHIVE => '📦',
            default => '📁'
        };
    }

    /**
     * Check if file is an image
     */
    public function isImage(): bool
    {
        return $this->type === self::TYPE_IMAGE;
    }

    /**
     * Check if file is a video
     */
    public function isVideo(): bool
    {
        return $this->type === self::TYPE_VIDEO;
    }

    /**
     * Get thumbnail URL
     */
    public function getThumbnailUrl(string $size = 'medium'): ?string
    {
        if (!$this->isImage() || !$this->thumbnails) {
            return null;
        }

        return $this->thumbnails[$size] ?? $this->file_url;
    }

    /**
     * Get full file URL
     */
    public function getFullUrl(): string
    {
        if (filter_var($this->file_url, FILTER_VALIDATE_URL)) {
            return $this->file_url;
        }
        
        return Storage::disk($this->disk)->url($this->file_path);
    }

    /**
     * Increment download count
     */
    public function incrementDownloadCount(): void
    {
        $this->increment('download_count');
        $this->update(['last_accessed_at' => now()]);
    }

    /**
     * Delete file from storage
     */
    public function deleteFile(): bool
    {
        // Delete thumbnails
        if ($this->thumbnails) {
            foreach ($this->thumbnails as $thumbnailPath) {
                if (Storage::disk($this->disk)->exists($thumbnailPath)) {
                    Storage::disk($this->disk)->delete($thumbnailPath);
                }
            }
        }

        // Delete main file
        if (Storage::disk($this->disk)->exists($this->file_path)) {
            Storage::disk($this->disk)->delete($this->file_path);
        }

        // Update status
        return $this->update(['status' => self::STATUS_DELETED]);
    }

    /**
     * Create media file from uploaded file
     */
    public static function createFromUpload(
        UploadedFile $file,
        User $user,
        ?string $folder = null,
        array $options = []
    ): self {
        $disk = $options['disk'] ?? 'public';
        $makePublic = $options['public'] ?? false;
        
        // Generate unique filename
        $extension = $file->getClientOriginalExtension();
        $filename = uniqid() . '_' . time() . '.' . $extension;
        
        // Determine folder path
        $folderPath = $folder ? "media/{$folder}" : 'media';
        $filePath = $folderPath . '/' . $filename;
        
        // Store file
        $storedPath = $file->storeAs($folderPath, $filename, $disk);
        $fileUrl = Storage::disk($disk)->url($storedPath);
        
        // Determine file type
        $mimeType = $file->getMimeType();
        $type = self::determineFileType($mimeType);
        
        // Create media file record
        $mediaFile = self::create([
            'user_id' => $user->id,
            'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $storedPath,
            'file_url' => $fileUrl,
            'disk' => $disk,
            'mime_type' => $mimeType,
            'extension' => $extension,
            'size' => $file->getSize(),
            'type' => $type,
            'folder' => $folder,
            'is_public' => $makePublic,
            'description' => $options['description'] ?? null,
            'alt_text' => $options['alt_text'] ?? null,
            'tags' => $options['tags'] ?? null,
        ]);
        
        // Generate thumbnails for images
        if ($type === self::TYPE_IMAGE) {
            $mediaFile->generateThumbnails();
        }
        
        return $mediaFile;
    }

    /**
     * Determine file type from MIME type
     */
    protected static function determineFileType(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) {
            return self::TYPE_IMAGE;
        }
        
        if (str_starts_with($mimeType, 'video/')) {
            return self::TYPE_VIDEO;
        }
        
        if (str_starts_with($mimeType, 'audio/')) {
            return self::TYPE_AUDIO;
        }
        
        $documentTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
            'text/csv'
        ];
        
        if (in_array($mimeType, $documentTypes)) {
            return self::TYPE_DOCUMENT;
        }
        
        $archiveTypes = [
            'application/zip',
            'application/x-rar-compressed',
            'application/x-tar',
            'application/gzip'
        ];
        
        if (in_array($mimeType, $archiveTypes)) {
            return self::TYPE_ARCHIVE;
        }
        
        return self::TYPE_OTHER;
    }

    /**
     * Generate thumbnails for image (placeholder - requires image processing package)
     */
    protected function generateThumbnails(): void
    {
        if (!$this->isImage()) {
            return;
        }

        // TODO: Implement thumbnail generation with image processing library
        // For now, just store original dimensions if available
        try {
            if (function_exists('getimagesize')) {
                $fullPath = Storage::disk($this->disk)->path($this->file_path);
                $imageInfo = getimagesize($fullPath);
                
                if ($imageInfo) {
                    $this->update([
                        'width' => $imageInfo[0],
                        'height' => $imageInfo[1]
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to get image dimensions: ' . $e->getMessage());
        }
    }

    /**
     * Search files
     */
    public static function search(string $query, array $filters = [])
    {
        $queryBuilder = self::query()->active();
        
        // Text search
        if (!empty($query)) {
            $queryBuilder->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('original_name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhereJsonContains('tags', $query);
            });
        }
        
        // Type filter
        if (!empty($filters['type'])) {
            $queryBuilder->where('type', $filters['type']);
        }
        
        // Folder filter
        if (!empty($filters['folder'])) {
            $queryBuilder->where('folder', $filters['folder']);
        }
        
        // Date range filter
        if (!empty($filters['date_from'])) {
            $queryBuilder->whereDate('created_at', '>=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $queryBuilder->whereDate('created_at', '<=', $filters['date_to']);
        }
        
        return $queryBuilder->orderBy('created_at', 'desc');
    }

    /**
     * Get folders list
     */
    public static function getFolders(): array
    {
        return self::active()
            ->whereNotNull('folder')
            ->distinct()
            ->pluck('folder')
            ->toArray();
    }
}