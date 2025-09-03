<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SupportTicket extends Model
{
    protected $fillable = [
        'ticket_number',
        'user_id',
        'assigned_to',
        'subject',
        'description',
        'priority',
        'status',
        'category',
        'tags',
        'department',
        'order_id',
        'product_id',
        'first_response_at',
        'last_response_at',
        'resolved_at',
        'closed_at',
        'response_time_minutes',
        'resolution_time_minutes',
        'messages_count',
        'satisfaction_rating',
        'satisfaction_feedback',
        'internal_notes',
        'is_escalated',
        'escalated_at'
    ];

    protected $casts = [
        'tags' => 'array',
        'first_response_at' => 'datetime',
        'last_response_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'escalated_at' => 'datetime',
        'is_escalated' => 'boolean',
    ];

    // Constants
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    const STATUS_OPEN = 'open';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_WAITING_CUSTOMER = 'waiting_customer';
    const STATUS_WAITING_ADMIN = 'waiting_admin';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_CLOSED = 'closed';

    const CATEGORY_GENERAL = 'general';
    const CATEGORY_TECHNICAL = 'technical';
    const CATEGORY_BILLING = 'billing';
    const CATEGORY_PRODUCT = 'product';
    const CATEGORY_SHIPPING = 'shipping';
    const CATEGORY_REFUND = 'refund';
    const CATEGORY_OTHER = 'other';

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class, 'ticket_id');
    }

    public function publicMessages(): HasMany
    {
        return $this->messages()->where('is_internal', false);
    }

    public function internalMessages(): HasMany
    {
        return $this->messages()->where('is_internal', true);
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->whereIn('status', [self::STATUS_OPEN, self::STATUS_IN_PROGRESS, self::STATUS_WAITING_CUSTOMER, self::STATUS_WAITING_ADMIN]);
    }

    public function scopeClosed($query)
    {
        return $query->whereIn('status', [self::STATUS_RESOLVED, self::STATUS_CLOSED]);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_to');
    }

    public function scopeEscalated($query)
    {
        return $query->where('is_escalated', true);
    }

    public function scopeOverdue($query, $hours = 24)
    {
        return $query->open()
                    ->where('created_at', '<', now()->subHours($hours))
                    ->whereNull('first_response_at');
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = self::generateTicketNumber();
            }
        });
    }

    // Utility Methods
    public static function generateTicketNumber(): string
    {
        do {
            $number = 'TKT-' . strtoupper(Str::random(8));
        } while (self::where('ticket_number', $number)->exists());

        return $number;
    }

    public function getPriorityLabel(): string
    {
        return match($this->priority) {
            self::PRIORITY_LOW => 'کم',
            self::PRIORITY_MEDIUM => 'متوسط',
            self::PRIORITY_HIGH => 'بالا',
            self::PRIORITY_URGENT => 'فوری',
            default => $this->priority
        };
    }

    public function getPriorityColor(): string
    {
        return match($this->priority) {
            self::PRIORITY_LOW => 'green',
            self::PRIORITY_MEDIUM => 'yellow',
            self::PRIORITY_HIGH => 'orange',
            self::PRIORITY_URGENT => 'red',
            default => 'gray'
        };
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            self::STATUS_OPEN => 'باز',
            self::STATUS_IN_PROGRESS => 'در حال بررسی',
            self::STATUS_WAITING_CUSTOMER => 'در انتظار مشتری',
            self::STATUS_WAITING_ADMIN => 'در انتظار پشتیبانی',
            self::STATUS_RESOLVED => 'حل شده',
            self::STATUS_CLOSED => 'بسته شده',
            default => $this->status
        };
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            self::STATUS_OPEN => 'blue',
            self::STATUS_IN_PROGRESS => 'yellow',
            self::STATUS_WAITING_CUSTOMER => 'orange',
            self::STATUS_WAITING_ADMIN => 'purple',
            self::STATUS_RESOLVED => 'green',
            self::STATUS_CLOSED => 'gray',
            default => 'gray'
        };
    }

    public function getCategoryLabel(): string
    {
        return match($this->category) {
            self::CATEGORY_GENERAL => 'عمومی',
            self::CATEGORY_TECHNICAL => 'فنی',
            self::CATEGORY_BILLING => 'مالی',
            self::CATEGORY_PRODUCT => 'محصول',
            self::CATEGORY_SHIPPING => 'ارسال',
            self::CATEGORY_REFUND => 'بازگشت وجه',
            self::CATEGORY_OTHER => 'سایر',
            default => $this->category
        };
    }

    public function isOpen(): bool
    {
        return in_array($this->status, [self::STATUS_OPEN, self::STATUS_IN_PROGRESS, self::STATUS_WAITING_CUSTOMER, self::STATUS_WAITING_ADMIN]);
    }

    public function isClosed(): bool
    {
        return in_array($this->status, [self::STATUS_RESOLVED, self::STATUS_CLOSED]);
    }

    public function isOverdue(int $hours = 24): bool
    {
        return $this->isOpen() && 
               $this->created_at->diffInHours(now()) > $hours && 
               !$this->first_response_at;
    }

    public function needsResponse(): bool
    {
        if (!$this->isOpen()) {
            return false;
        }

        $lastMessage = $this->messages()->latest()->first();
        
        return !$lastMessage || 
               (!$lastMessage->user->isAdmin() && $lastMessage->created_at->diffInHours(now()) > 2);
    }

    // Status Management
    public function updateStatus(string $newStatus, ?User $user = null, ?string $note = null): void
    {
        $oldStatus = $this->status;
        $this->status = $newStatus;

        // Set timestamps based on status
        switch ($newStatus) {
            case self::STATUS_RESOLVED:
                $this->resolved_at = now();
                if ($this->created_at) {
                    $this->resolution_time_minutes = $this->created_at->diffInMinutes(now());
                }
                break;
            case self::STATUS_CLOSED:
                $this->closed_at = now();
                break;
        }

        $this->save();

        // Create system message for status change
        $this->addSystemMessage(
            "وضعیت تیکت از '{$this->getStatusLabel($oldStatus)}' به '{$this->getStatusLabel()}' تغییر یافت.",
            $user,
            TicketMessage::TYPE_STATUS_CHANGE,
            ['old_status' => $oldStatus, 'new_status' => $newStatus, 'note' => $note]
        );
    }

    public function assignTo(?User $user, ?User $assignedBy = null): void
    {
        $oldAssignee = $this->assignedTo;
        $this->assigned_to = $user?->id;
        $this->save();

        // Create system message for assignment
        if ($user) {
            $message = "تیکت به {$user->name} واگذار شد.";
        } else {
            $message = "واگذاری تیکت لغو شد.";
        }

        $this->addSystemMessage(
            $message,
            $assignedBy,
            TicketMessage::TYPE_ASSIGNMENT,
            ['old_assignee_id' => $oldAssignee?->id, 'new_assignee_id' => $user?->id]
        );
    }

    public function escalate(?User $escalatedBy = null, ?string $reason = null): void
    {
        $this->is_escalated = true;
        $this->escalated_at = now();
        $this->priority = self::PRIORITY_URGENT;
        $this->save();

        $message = 'تیکت ارجاع داده شد.';
        if ($reason) {
            $message .= " دلیل: {$reason}";
        }

        $this->addSystemMessage(
            $message,
            $escalatedBy,
            TicketMessage::TYPE_ESCALATION,
            ['reason' => $reason]
        );
    }

    // Message Management
    public function addMessage(string $message, User $user, array $attachments = [], bool $isInternal = false): TicketMessage
    {
        $ticketMessage = $this->messages()->create([
            'user_id' => $user->id,
            'message' => $message,
            'attachments' => $attachments,
            'is_internal' => $isInternal,
            'type' => TicketMessage::TYPE_MESSAGE
        ]);

        // Update ticket timestamps and counters
        $this->increment('messages_count');
        $this->last_response_at = now();

        // Set first response time if this is the first admin response
        if ($user->isAdmin() && !$this->first_response_at) {
            $this->first_response_at = now();
            $this->response_time_minutes = $this->created_at->diffInMinutes(now());
        }

        // Update status based on who responded
        if ($user->isAdmin() && $this->status === self::STATUS_WAITING_ADMIN) {
            $this->status = self::STATUS_WAITING_CUSTOMER;
        } elseif (!$user->isAdmin() && $this->status === self::STATUS_WAITING_CUSTOMER) {
            $this->status = self::STATUS_WAITING_ADMIN;
        }

        $this->save();

        return $ticketMessage;
    }

    public function addSystemMessage(string $message, ?User $user = null, string $type = TicketMessage::TYPE_MESSAGE, array $systemData = []): TicketMessage
    {
        return $this->messages()->create([
            'user_id' => $user?->id,
            'message' => $message,
            'type' => $type,
            'is_system_generated' => true,
            'system_data' => $systemData,
            'is_internal' => true
        ]);
    }

    // Rating and Feedback
    public function addRating(int $rating, ?string $feedback = null): void
    {
        $this->satisfaction_rating = max(1, min(5, $rating));
        $this->satisfaction_feedback = $feedback;
        $this->save();

        $this->addSystemMessage(
            "مشتری امتیاز {$rating} از 5 به این تیکت داد.",
            null,
            TicketMessage::TYPE_MESSAGE,
            ['rating' => $rating, 'feedback' => $feedback]
        );
    }

    // Statistics
    public function getResponseTimeHours(): ?float
    {
        return $this->response_time_minutes ? round($this->response_time_minutes / 60, 1) : null;
    }

    public function getResolutionTimeHours(): ?float
    {
        return $this->resolution_time_minutes ? round($this->resolution_time_minutes / 60, 1) : null;
    }

    public function getAgeDays(): int
    {
        return $this->created_at->diffInDays(now());
    }

    public function getLastActivityDays(): int
    {
        $lastActivity = $this->last_response_at ?: $this->created_at;
        return $lastActivity->diffInDays(now());
    }

    // Static Methods
    public static function getStatistics(): array
    {
        return [
            'total' => self::count(),
            'open' => self::open()->count(),
            'closed' => self::closed()->count(),
            'unassigned' => self::unassigned()->count(),
            'escalated' => self::escalated()->count(),
            'overdue' => self::overdue()->count(),
            'avg_response_time' => self::whereNotNull('response_time_minutes')->avg('response_time_minutes'),
            'avg_resolution_time' => self::whereNotNull('resolution_time_minutes')->avg('resolution_time_minutes'),
            'satisfaction_avg' => self::whereNotNull('satisfaction_rating')->avg('satisfaction_rating'),
        ];
    }

    public static function getPriorityOptions(): array
    {
        return [
            self::PRIORITY_LOW => 'کم',
            self::PRIORITY_MEDIUM => 'متوسط',
            self::PRIORITY_HIGH => 'بالا',
            self::PRIORITY_URGENT => 'فوری',
        ];
    }

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_OPEN => 'باز',
            self::STATUS_IN_PROGRESS => 'در حال بررسی',
            self::STATUS_WAITING_CUSTOMER => 'در انتظار مشتری',
            self::STATUS_WAITING_ADMIN => 'در انتظار پشتیبانی',
            self::STATUS_RESOLVED => 'حل شده',
            self::STATUS_CLOSED => 'بسته شده',
        ];
    }

    public static function getCategoryOptions(): array
    {
        return [
            self::CATEGORY_GENERAL => 'عمومی',
            self::CATEGORY_TECHNICAL => 'فنی',
            self::CATEGORY_BILLING => 'مالی',
            self::CATEGORY_PRODUCT => 'محصول',
            self::CATEGORY_SHIPPING => 'ارسال',
            self::CATEGORY_REFUND => 'بازگشت وجه',
            self::CATEGORY_OTHER => 'سایر',
        ];
    }


}