<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Category;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $stats = [
            'total_users' => User::count(),
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'total_categories' => Category::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'total_revenue' => Order::where('status', 'completed')->sum('total_amount'),
            'recent_orders' => Order::with('user')->latest()->take(5)->get(),
        ];

        return view('livewire.admin.dashboard', compact('stats'))
            ->layout('layouts.admin')
            ->section('title', 'داشبورد');
    }
}
