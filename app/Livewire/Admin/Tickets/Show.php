<?php

namespace App\Livewire\Admin\Tickets;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\SupportTicket;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Show extends Component
{
    use WithFileUploads;

    protected $layout = 'layouts.admin';

    public SupportTicket $ticket;

    // Message form
    public $newMessage = '';
    public $attachments = [];
    public $isInternal = false;

    // Ticket management
    public $editingTicket = false;
    public $editSubject = '';
    public $editPriority = '';
    public $editCategory = '';
    public $editAssignee = '';
    public $editStatus = '';
    public $editTags = '';
    public $internalNotes = '';

    // Rating (for closed tickets)
    public $showRatingForm = false;
    public $rating = 5;
    public $ratingFeedback = '';

    protected $rules = [
        'newMessage' => 'required|string|min:3',
        'attachments.*' => 'file|max:10240', // 10MB max
        'editSubject' => 'required|string|max:255',
        'editPriority' => 'required|in:low,medium,high,urgent',
        'editCategory' => 'required|string',
        'editStatus' => 'required|string',
        'rating' => 'required|integer|min:1|max:5',
    ];

    protected $listeners = [
        'ticketUpdated' => 'refreshTicket'
    ];

    public function mount(SupportTicket $ticket)
    {
        $this->ticket = $ticket;
        $this->loadTicketData();
    }

    public function render()
    {
        $messages = $this->ticket->messages()
                                ->with('user')
                                ->orderBy('created_at', 'asc')
                                ->get();

        $admins = User::where('role', 'admin')->get();

        return view('livewire.admin.tickets.show', [
            'messages' => $messages,
            'admins' => $admins,
            'priorityOptions' => SupportTicket::getPriorityOptions(),
            'statusOptions' => SupportTicket::getStatusOptions(),
            'categoryOptions' => SupportTicket::getCategoryOptions()
        ]);
    }

    public function loadTicketData()
    {
        $this->editSubject = $this->ticket->subject;
        $this->editPriority = $this->ticket->priority;
        $this->editCategory = $this->ticket->category;
        $this->editAssignee = $this->ticket->assigned_to;
        $this->editStatus = $this->ticket->status;
        $this->editTags = $this->ticket->tags ? implode(', ', $this->ticket->tags) : '';
        $this->internalNotes = $this->ticket->internal_notes ?? '';
    }

    public function refreshTicket()
    {
        $this->ticket->refresh();
        $this->loadTicketData();
    }

    public function sendMessage()
    {
        $this->validate([
            'newMessage' => 'required|string|min:3',
            'attachments.*' => 'file|max:10240'
        ]);

        $attachmentData = [];

        // Handle file uploads
        if (!empty($this->attachments)) {
            foreach ($this->attachments as $file) {
                $path = $file->store('ticket-attachments', 'public');
                $attachmentData[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType()
                ];
            }
        }

        // Add message to ticket
        $this->ticket->addMessage(
            $this->newMessage,
            Auth::user(),
            $attachmentData,
            $this->isInternal
        );

        // Reset form
        $this->newMessage = '';
        $this->attachments = [];
        $this->isInternal = false;

        $this->dispatch('ticketUpdated');
        session()->flash('message', 'پیام ارسال شد.');
    }

    public function toggleEdit()
    {
        $this->editingTicket = !$this->editingTicket;
        if (!$this->editingTicket) {
            $this->loadTicketData();
        }
    }

    public function updateTicket()
    {
        $this->validate([
            'editSubject' => 'required|string|max:255',
            'editPriority' => 'required|in:low,medium,high,urgent',
            'editCategory' => 'required|string',
            'editStatus' => 'required|string',
        ]);

        $oldStatus = $this->ticket->status;
        $oldAssignee = $this->ticket->assigned_to;

        $tags = $this->editTags ? array_map('trim', explode(',', $this->editTags)) : null;

        $this->ticket->update([
            'subject' => $this->editSubject,
            'priority' => $this->editPriority,
            'category' => $this->editCategory,
            'status' => $this->editStatus,
            'assigned_to' => $this->editAssignee ?: null,
            'tags' => $tags,
            'internal_notes' => $this->internalNotes
        ]);

        // Create system messages for changes
        if ($oldStatus !== $this->editStatus) {
            $this->ticket->addSystemMessage(
                "وضعیت تیکت از '{$this->ticket->getStatusLabel($oldStatus)}' به '{$this->ticket->getStatusLabel()}' تغییر یافت.",
                Auth::user(),
                'status_change'
            );
        }

        if ($oldAssignee != $this->editAssignee) {
            $newAssignee = $this->editAssignee ? User::find($this->editAssignee) : null;
            $message = $newAssignee ? "تیکت به {$newAssignee->name} واگذار شد." : "واگذاری تیکت لغو شد.";

            $this->ticket->addSystemMessage(
                $message,
                Auth::user(),
                'assignment'
            );
        }

        $this->editingTicket = false;
        $this->dispatch('ticketUpdated');
        session()->flash('message', 'تیکت به‌روزرسانی شد.');
    }

    public function assignToMe()
    {
        $this->ticket->assignTo(Auth::user(), Auth::user());
        $this->refreshTicket();
        session()->flash('message', 'تیکت به شما واگذار شد.');
    }

    public function escalateTicket()
    {
        $this->ticket->escalate(Auth::user(), 'ارجاع توسط ادمین');
        $this->refreshTicket();
        session()->flash('message', 'تیکت ارجاع داده شد.');
    }

    public function closeTicket()
    {
        $this->ticket->updateStatus(SupportTicket::STATUS_CLOSED, Auth::user());
        $this->refreshTicket();
        session()->flash('message', 'تیکت بسته شد.');
    }

    public function reopenTicket()
    {
        $this->ticket->updateStatus(SupportTicket::STATUS_OPEN, Auth::user());
        $this->refreshTicket();
        session()->flash('message', 'تیکت مجدداً باز شد.');
    }

    public function markAsResolved()
    {
        $this->ticket->updateStatus(SupportTicket::STATUS_RESOLVED, Auth::user());
        $this->refreshTicket();
        session()->flash('message', 'تیکت به عنوان حل شده علامت‌گذاری شد.');
    }

    public function showRatingForm()
    {
        $this->showRatingForm = true;
    }

    public function submitRating()
    {
        $this->validate([
            'rating' => 'required|integer|min:1|max:5'
        ]);

        $this->ticket->addRating($this->rating, $this->ratingFeedback);

        $this->showRatingForm = false;
        $this->rating = 5;
        $this->ratingFeedback = '';

        $this->refreshTicket();
        session()->flash('message', 'امتیاز ثبت شد.');
    }

    public function downloadAttachment($messageId, $attachmentIndex)
    {
        $message = $this->ticket->messages()->find($messageId);

        if ($message && isset($message->attachments[$attachmentIndex])) {
            $attachment = $message->attachments[$attachmentIndex];

            if (Storage::disk('public')->exists($attachment['path'])) {
                return response()->download(
                    Storage::disk('public')->path($attachment['path']),
                    $attachment['name']
                );
            }
        }

        session()->flash('error', 'فایل یافت نشد.');
    }

    public function deleteMessage($messageId)
    {
        $message = $this->ticket->messages()->find($messageId);

        if ($message && ($message->user_id === Auth::id() || Auth::user()->role === 'admin')) {
            // Delete attachments
            if ($message->attachments) {
                foreach ($message->attachments as $attachment) {
                    if (Storage::disk('public')->exists($attachment['path'])) {
                        Storage::disk('public')->delete($attachment['path']);
                    }
                }
            }

            $message->delete();
            $this->ticket->decrement('messages_count');

            session()->flash('message', 'پیام حذف شد.');
        }
    }

    public function getTicketAge()
    {
        return $this->ticket->created_at->diffForHumans();
    }

    public function getLastActivity()
    {
        $lastActivity = $this->ticket->last_response_at ?: $this->ticket->created_at;
        return $lastActivity->diffForHumans();
    }

    public function canEditTicket()
    {
        return Auth::user()->role === 'admin' || $this->ticket->assigned_to === Auth::id();
    }

    public function canCloseTicket()
    {
        return $this->ticket->isOpen() && $this->canEditTicket();
    }

    public function canReopenTicket()
    {
        return $this->ticket->isClosed() && Auth::user()->role === 'admin';
    }
}
