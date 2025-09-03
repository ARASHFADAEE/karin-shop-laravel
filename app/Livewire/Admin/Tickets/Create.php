<?php

namespace App\Livewire\Admin\Tickets;

use Livewire\Component;
use App\Models\SupportTicket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class Create extends Component
{
    protected $layout = 'layouts.admin';

    // Form properties
    public $subject = '';
    public $description = '';
    public $priority = 'medium';
    public $category = 'general';
    public $user_id = '';
    public $order_id = '';
    public $product_id = '';
    public $tags = '';
    public $department = 'support';

    protected $rules = [
        'subject' => 'required|string|max:255',
        'description' => 'required|string',
        'priority' => 'required|in:low,medium,high,urgent',
        'category' => 'required|in:general,technical,billing,product,order,complaint,suggestion',
        'user_id' => 'nullable|exists:users,id',
        'order_id' => 'nullable|exists:orders,id',
        'product_id' => 'nullable|exists:products,id',
        'tags' => 'nullable|string',
        'department' => 'required|in:support,technical,sales,billing'
    ];

    protected $messages = [
        'subject.required' => 'موضوع تیکت الزامی است.',
        'description.required' => 'توضیحات تیکت الزامی است.',
        'priority.required' => 'اولویت تیکت الزامی است.',
        'category.required' => 'دسته‌بندی تیکت الزامی است.',
        'user_id.exists' => 'کاربر انتخاب شده معتبر نیست.',
        'order_id.exists' => 'سفارش انتخاب شده معتبر نیست.',
        'product_id.exists' => 'محصول انتخاب شده معتبر نیست.',
        'department.required' => 'بخش مسئول الزامی است.'
    ];

    public function render()
    {
        $users = User::orderBy('name')->get();
        $orders = Order::with('user')->latest()->take(100)->get();
        $products = Product::orderBy('name')->get();

        return view('livewire.admin.tickets.create', [
            'users' => $users,
            'orders' => $orders,
            'products' => $products,
            'priorityOptions' => SupportTicket::getPriorityOptions(),
            'statusOptions' => SupportTicket::getStatusOptions(),
            'categoryOptions' => SupportTicket::getCategoryOptions(),
            'departmentOptions' => $this->getDepartmentOptions()
        ]);
    }

    public function createTicket()
    {
        $this->validate();

        $ticket = SupportTicket::create([
            'ticket_number' => SupportTicket::generateTicketNumber(),
            'user_id' => $this->user_id ?: null,
            'assigned_to' => Auth::id(),
            'subject' => $this->subject,
            'description' => $this->description,
            'priority' => $this->priority,
            'status' => 'open',
            'category' => $this->category,
            'order_id' => $this->order_id ?: null,
            'product_id' => $this->product_id ?: null,
            'tags' => $this->tags ? explode(',', $this->tags) : null,
            'department' => $this->department,
            'created_by_admin' => true
        ]);

        // Add initial system message
        $ticket->addSystemMessage(
            'تیکت توسط ادمین ایجاد شد.',
            Auth::user(),
            TicketMessage::TYPE_MESSAGE,
            [
                'admin_id' => Auth::id(),
                'admin_name' => Auth::user()->name
            ]
        );

        session()->flash('success', 'تیکت با موفقیت ایجاد شد.');

        return redirect()->route('admin.tickets.show', $ticket);
    }

    public function resetForm()
    {
        $this->reset([
            'subject', 'description', 'priority', 'category',
            'user_id', 'order_id', 'product_id', 'tags', 'department'
        ]);
        $this->priority = 'medium';
        $this->category = 'general';
        $this->department = 'support';
    }

    public function getDepartmentOptions()
    {
        return [
            'support' => 'پشتیبانی',
            'technical' => 'فنی',
            'sales' => 'فروش',
            'billing' => 'مالی'
        ];
    }

    public function updatedUserId($value)
    {
        if ($value) {
            $user = User::find($value);
            if ($user) {
                // Auto-fill related orders if user is selected
                $this->dispatch('userSelected', $user->id);
            }
        }
    }

    public function updatedOrderId($value)
    {
        if ($value) {
            $order = Order::with('user')->find($value);
            if ($order && $order->user) {
                $this->user_id = $order->user->id;
            }
        }
    }
}
