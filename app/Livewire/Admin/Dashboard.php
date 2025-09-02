<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Dashboard extends Component
{
    public function getStats()
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        
        return [
            // Basic Stats
            'total_users' => User::count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'total_products' => Product::count(),
            'active_products' => Product::where('status', 'active')->count(),
            'total_orders' => Order::count(),
            'total_categories' => Category::count(),
            
            // Revenue Stats
            'total_revenue' => Order::where('status', 'completed')->sum('total_amount'),
            'today_revenue' => Order::where('status', 'completed')
                ->whereDate('created_at', $today)
                ->sum('total_amount'),
            'this_week_revenue' => Order::where('status', 'completed')
                ->where('created_at', '>=', $thisWeek)
                ->sum('total_amount'),
            'this_month_revenue' => Order::where('status', 'completed')
                ->where('created_at', '>=', $thisMonth)
                ->sum('total_amount'),
            'last_month_revenue' => Order::where('status', 'completed')
                ->whereBetween('created_at', [$lastMonth, $thisMonth])
                ->sum('total_amount'),
            
            // Order Stats
            'pending_orders' => Order::where('status', 'pending')->count(),
            'paid_orders' => Order::where('status', 'paid')->count(),
            'shipped_orders' => Order::where('status', 'shipped')->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'canceled_orders' => Order::where('status', 'canceled')->count(),
            
            'today_orders' => Order::whereDate('created_at', $today)->count(),
            'this_week_orders' => Order::where('created_at', '>=', $thisWeek)->count(),
            'this_month_orders' => Order::where('created_at', '>=', $thisMonth)->count(),
            
            // Product Stats
            'low_stock_products' => Product::where('stock', '<=', 5)->count(),
            'out_of_stock_products' => Product::where('stock', 0)->count(),
            
            // Recent Activity
            'recent_orders' => Order::with(['user', 'orderItems.product'])
                ->latest()
                ->take(10)
                ->get(),
            'recent_users' => User::where('role', 'customer')
                ->latest()
                ->take(5)
                ->get(),
            'low_stock_items' => Product::where('stock', '<=', 5)
                ->where('stock', '>', 0)
                ->orderBy('stock', 'asc')
                ->take(5)
                ->get(),
        ];
    }
    
    public function getSalesChart()
    {
        $last30Days = collect();
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $sales = Order::where('status', 'completed')
                ->whereDate('created_at', $date)
                ->sum('total_amount');
            
            $last30Days->push([
                'date' => $date->format('Y-m-d'),
                'sales' => $sales,
                'label' => $date->format('M d')
            ]);
        }
        
        return $last30Days;
    }
    
    public function getOrdersChart()
    {
        $last30Days = collect();
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $orders = Order::whereDate('created_at', $date)->count();
            
            $last30Days->push([
                'date' => $date->format('Y-m-d'),
                'orders' => $orders,
                'label' => $date->format('M d')
            ]);
        }
        
        return $last30Days;
    }
    
    public function getTopProducts()
    {
        return DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->select(
                'products.name',
                'products.sku',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.quantity * order_items.price) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderBy('total_sold', 'desc')
            ->take(5)
            ->get();
    }
    
    public function getTopCategories()
    {
        return DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->select(
                'categories.name',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.quantity * order_items.price) as total_revenue')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_revenue', 'desc')
            ->take(5)
            ->get();
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        $stats = $this->getStats();
        $salesChart = $this->getSalesChart();
        $ordersChart = $this->getOrdersChart();
        $topProducts = $this->getTopProducts();
        $topCategories = $this->getTopCategories();
        
        return view('livewire.admin.dashboard', compact(
            'stats',
            'salesChart', 
            'ordersChart',
            'topProducts',
            'topCategories'
        ));
    }
}
