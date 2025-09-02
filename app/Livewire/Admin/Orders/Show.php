<?php

namespace App\Livewire\Admin\Orders;

use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Show extends Component
{
    public Order $order;

    public function mount(Order $order)
    {
        $this->order = $order->load(['user', 'orderItems.product', 'payment']);
    }

    public function updateStatus($newStatus)
    {
        $this->order->update(['status' => $newStatus]);
        $this->order->refresh();
        session()->flash('success', 'وضعیت سفارش به‌روزرسانی شد.');
    }

    public function calculateTotal()
    {
        return $this->order->orderItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });
    }

    public function printOrder()
    {
        // Logic for printing order
        session()->flash('success', 'سفارش برای چاپ آماده شد.');
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.admin.orders.show');
    }
}
