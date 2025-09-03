<?php

namespace App\Livewire\Admin\Tickets;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    protected $layout = 'layouts.admin';

    // Filter properties
    public $search = '';
    public $filterStatus = '';
    public $filterPriority = '';
    public $filterCategory = '';
    public $filterAssignee = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 20;

    // UI properties
    public $showFilters = false;
    public $selectedTickets = [];
    public $selectAll = false;

    // Bulk actions
    public $bulkAction = '';
    public $bulkAssignee = '';
    public $bulkStatus = '';
    public $bulkPriority = '';

    protected $listeners = [
        'ticketUpdated' => '$refresh',
        'refreshTickets' => '$refresh'
    ];

    public function mount()
    {
        $this->resetPage();
    }

    public function render()
    {
        $tickets = $this->getTickets();
        $stats = $this->getStats();
        $admins = User::where('role', 'admin')->get();

        return view('livewire.admin.tickets.index', [
            'tickets' => $tickets,
            'stats' => $stats,
            'admins' => $admins,
            'priorityOptions' => SupportTicket::getPriorityOptions(),
            'statusOptions' => SupportTicket::getStatusOptions(),
            'categoryOptions' => SupportTicket::getCategoryOptions()
        ]);
    }

    public function getTickets()
    {
        $query = SupportTicket::with(['user', 'assignedTo', 'order', 'product'])
                              ->when($this->search, function($q) {
                                  $q->where(function($query) {
                                      $query->where('ticket_number', 'like', "%{$this->search}%")
                                            ->orWhere('subject', 'like', "%{$this->search}%")
                                            ->orWhere('description', 'like', "%{$this->search}%")
                                            ->orWhereHas('user', function($userQuery) {
                                                $userQuery->where('name', 'like', "%{$this->search}%")
                                                         ->orWhere('email', 'like', "%{$this->search}%");
                                            });
                                  });
                              })
                              ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
                              ->when($this->filterPriority, fn($q) => $q->where('priority', $this->filterPriority))
                              ->when($this->filterCategory, fn($q) => $q->where('category', $this->filterCategory))
                              ->when($this->filterAssignee, function($q) {
                                  if ($this->filterAssignee === 'unassigned') {
                                      $q->whereNull('assigned_to');
                                  } else {
                                      $q->where('assigned_to', $this->filterAssignee);
                                  }
                              })
                              ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
                              ->when($this->dateTo, fn($q) => $q->whereDate('created_at', '<=', $this->dateTo));

        return $query->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
    }

    public function getStats()
    {
        return [
            'total' => SupportTicket::count(),
            'open' => SupportTicket::open()->count(),
            'closed' => SupportTicket::closed()->count(),
            'unassigned' => SupportTicket::unassigned()->count(),
            'overdue' => SupportTicket::overdue()->count(),
            'my_tickets' => SupportTicket::where('assigned_to', Auth::id())->open()->count(),
        ];
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->filterStatus = '';
        $this->filterPriority = '';
        $this->filterCategory = '';
        $this->filterAssignee = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->resetPage();
    }

    public function setSortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'desc';
        }
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function updatedFilterPriority()
    {
        $this->resetPage();
    }

    public function updatedFilterCategory()
    {
        $this->resetPage();
    }

    public function updatedFilterAssignee()
    {
        $this->resetPage();
    }

    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedTickets = $this->getTickets()->pluck('id')->toArray();
        } else {
            $this->selectedTickets = [];
        }
    }

    public function assignTicket($ticketId, $userId)
    {
        $ticket = SupportTicket::find($ticketId);
        $user = $userId ? User::find($userId) : null;

        if ($ticket) {
            $ticket->assignTo($user, Auth::user());
            $this->dispatch('ticketUpdated');
            session()->flash('message', 'تیکت با موفقیت واگذار شد.');
        }
    }

    public function updateTicketStatus($ticketId, $status)
    {
        $ticket = SupportTicket::find($ticketId);

        if ($ticket) {
            $ticket->updateStatus($status, Auth::user());
            $this->dispatch('ticketUpdated');
            session()->flash('message', 'وضعیت تیکت به‌روزرسانی شد.');
        }
    }

    public function updateTicketPriority($ticketId, $priority)
    {
        $ticket = SupportTicket::find($ticketId);

        if ($ticket) {
            $ticket->update(['priority' => $priority]);
            $this->dispatch('ticketUpdated');
            session()->flash('message', 'اولویت تیکت به‌روزرسانی شد.');
        }
    }

    public function escalateTicket($ticketId)
    {
        $ticket = SupportTicket::find($ticketId);

        if ($ticket) {
            $ticket->escalate(Auth::user(), 'ارجاع توسط ادمین');
            $this->dispatch('ticketUpdated');
            session()->flash('message', 'تیکت ارجاع داده شد.');
        }
    }

    public function executeBulkAction()
    {
        if (empty($this->selectedTickets) || empty($this->bulkAction)) {
            session()->flash('error', 'لطفاً تیکت‌ها و عملیات را انتخاب کنید.');
            return;
        }

        $tickets = SupportTicket::whereIn('id', $this->selectedTickets)->get();
        $count = 0;

        foreach ($tickets as $ticket) {
            switch ($this->bulkAction) {
                case 'assign':
                    if ($this->bulkAssignee) {
                        $user = User::find($this->bulkAssignee);
                        $ticket->assignTo($user, Auth::user());
                        $count++;
                    }
                    break;

                case 'status':
                    if ($this->bulkStatus) {
                        $ticket->updateStatus($this->bulkStatus, Auth::user());
                        $count++;
                    }
                    break;

                case 'priority':
                    if ($this->bulkPriority) {
                        $ticket->update(['priority' => $this->bulkPriority]);
                        $count++;
                    }
                    break;

                case 'escalate':
                    $ticket->escalate(Auth::user(), 'ارجاع گروهی');
                    $count++;
                    break;
            }
        }

        $this->selectedTickets = [];
        $this->selectAll = false;
        $this->bulkAction = '';
        $this->bulkAssignee = '';
        $this->bulkStatus = '';
        $this->bulkPriority = '';

        $this->dispatch('ticketUpdated');
        session()->flash('message', "{$count} تیکت به‌روزرسانی شد.");
    }

    public function setQuickFilter($filter)
    {
        $this->clearFilters();

        switch ($filter) {
            case 'my_tickets':
                $this->filterAssignee = Auth::id();
                break;
            case 'unassigned':
                $this->filterAssignee = 'unassigned';
                break;
            case 'open':
                $this->filterStatus = SupportTicket::STATUS_OPEN;
                break;
            case 'overdue':
                // This would need a custom scope or filter logic
                break;
            case 'urgent':
                $this->filterPriority = SupportTicket::PRIORITY_URGENT;
                break;
        }

        $this->resetPage();
    }

    public function exportTickets()
    {
        // TODO: Implement export functionality
        session()->flash('message', 'قابلیت خروجی به زودی اضافه خواهد شد.');
    }

    public function createTicket()
    {
        return redirect()->route('admin.tickets.create');
    }

    public function assignToMe($ticketId)
    {
        $ticket = SupportTicket::find($ticketId);
        
        if ($ticket) {
            $ticket->assignTo(Auth::user(), Auth::user());
            $this->dispatch('ticketUpdated');
            session()->flash('message', 'تیکت به شما واگذار شد.');
        }
    }
}
