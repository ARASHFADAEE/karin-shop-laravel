<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketMessage extends Model
{
    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'attachments',
        'type',
        'is_internal',
        'is_read',
        'read_at',
        'is_system_generated',
        'system_data'
    ];

    protected $casts = [
        'attachments' => 'array',
        'system_data' => 'array',
        'is_internal' => 'boolean',
        'is_read' => 'boolean',
        'is_system_generated' => 'boolean',
        'read_at' => 'datetime',
    ];

    // Constants
    const TYPE_MESSAGE = 'message';
    const TYPE_NOTE = 'note';
    const TYPE_STATUS_CHANGE = 'status_change';
    const TYPE_ASSIGNMENT = 'assignment';
    const TYPE_ESCALATION = 'escalation';

    // Relationships
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('is_internal', false);
    }

    public function scopeInternal($query)
    {
        return $query->where('is_internal', true);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Utility Methods
    public function markAsRead(?User $readBy = null): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    public function getTypeLabel(): string
    {
        return match($this->type) {
            self::TYPE_MESSAGE => 'پیام',
            self::TYPE_NOTE => 'یادداشت',
            self::TYPE_STATUS_CHANGE => 'تغییر وضعیت',
            self::TYPE_ASSIGNMENT => 'واگذاری',
            self::TYPE_ESCALATION => 'ارجاع',
            default => $this->type
        };
    }

    public function isFromAdmin(): bool
    {
        return $this->user && $this->user->isAdmin();
    }

    public function isFromCustomer(): bool
    {
        return $this->user && !$this->user->isAdmin();
    }

    public function hasAttachments(): bool
    {
        return !empty($this->attachments);
    }

    public function getFormattedMessage(): string
    {
        // Convert line breaks to HTML
        return nl2br(e($this->message));
    }

    public function getTimeAgo(): string
    {
        return $this->created_at->diffForHumans();
    }
}