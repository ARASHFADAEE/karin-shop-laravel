<div>
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">داشبورد مدیریت</h1>
        <p class="text-gray-600 mt-2">خلاصه‌ای از عملکرد فروشگاه شما</p>
    </div>

    <!-- Main Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Revenue -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 overflow-hidden shadow-lg rounded-lg text-white">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="mr-4">
                        <p class="text-sm font-medium opacity-90">کل درآمد</p>
                        <p class="text-2xl font-bold">{{ number_format($stats['total_revenue']) }} تومان</p>
                        <p class="text-xs opacity-75 mt-1">امروز: {{ number_format($stats['today_revenue']) }} تومان</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 overflow-hidden shadow-lg rounded-lg text-white">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div class="mr-4">
                        <p class="text-sm font-medium opacity-90">کل سفارشات</p>
                        <p class="text-2xl font-bold">{{ number_format($stats['total_orders']) }}</p>
                        <p class="text-xs opacity-75 mt-1">امروز: {{ number_format($stats['today_orders']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Users -->
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 overflow-hidden shadow-lg rounded-lg text-white">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <div class="mr-4">
                        <p class="text-sm font-medium opacity-90">کل کاربران</p>
                        <p class="text-2xl font-bold">{{ number_format($stats['total_users']) }}</p>
                        <p class="text-xs opacity-75 mt-1">مشتریان: {{ number_format($stats['total_customers']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Products -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 overflow-hidden shadow-lg rounded-lg text-white">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div class="mr-4">
                        <p class="text-sm font-medium opacity-90">کل محصولات</p>
                        <p class="text-2xl font-bold">{{ number_format($stats['total_products']) }}</p>
                        <p class="text-xs opacity-75 mt-1">فعال: {{ number_format($stats['active_products']) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Comparison -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-gray-900 mb-4">درآمد این هفته</h3>
            <p class="text-3xl font-bold text-green-600">{{ number_format($stats['this_week_revenue']) }}</p>
            <p class="text-sm text-gray-500 mt-1">تومان</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-gray-900 mb-4">درآمد این ماه</h3>
            <p class="text-3xl font-bold text-blue-600">{{ number_format($stats['this_month_revenue']) }}</p>
            <p class="text-sm text-gray-500 mt-1">تومان</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-gray-900 mb-4">درآمد ماه گذشته</h3>
            <p class="text-3xl font-bold text-gray-600">{{ number_format($stats['last_month_revenue']) }}</p>
            <p class="text-sm text-gray-500 mt-1">تومان</p>
        </div>
    </div>

    <!-- Order Status Overview -->
    <div class="bg-white shadow rounded-lg p-6 mb-8">
        <h3 class="text-lg font-medium text-gray-900 mb-6">وضعیت سفارشات</h3>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="text-center">
                <div class="bg-yellow-100 rounded-full p-3 w-16 h-16 mx-auto mb-2 flex items-center justify-center">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending_orders'] }}</p>
                <p class="text-sm text-gray-600">در انتظار</p>
            </div>
            <div class="text-center">
                <div class="bg-blue-100 rounded-full p-3 w-16 h-16 mx-auto mb-2 flex items-center justify-center">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-blue-600">{{ $stats['paid_orders'] }}</p>
                <p class="text-sm text-gray-600">پرداخت شده</p>
            </div>
            <div class="text-center">
                <div class="bg-indigo-100 rounded-full p-3 w-16 h-16 mx-auto mb-2 flex items-center justify-center">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-indigo-600">{{ $stats['shipped_orders'] }}</p>
                <p class="text-sm text-gray-600">ارسال شده</p>
            </div>
            <div class="text-center">
                <div class="bg-green-100 rounded-full p-3 w-16 h-16 mx-auto mb-2 flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-green-600">{{ $stats['completed_orders'] }}</p>
                <p class="text-sm text-gray-600">تکمیل شده</p>
            </div>
            <div class="text-center">
                <div class="bg-red-100 rounded-full p-3 w-16 h-16 mx-auto mb-2 flex items-center justify-center">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-red-600">{{ $stats['canceled_orders'] }}</p>
                <p class="text-sm text-gray-600">لغو شده</p>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Sales Chart -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">نمودار فروش (30 روز گذشته)</h3>
            <div class="h-64">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Orders Chart -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">نمودار سفارشات (30 روز گذشته)</h3>
            <div class="h-64">
                <canvas id="ordersChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Products & Categories -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Top Products -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">محصولات پرفروش</h3>
            @if($topProducts->count() > 0)
                <div class="space-y-4">
                    @foreach($topProducts as $product)
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900">{{ $product->name }}</p>
                                <p class="text-sm text-gray-500">SKU: {{ $product->sku }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-green-600">{{ number_format($product->total_sold) }} فروش</p>
                                <p class="text-sm text-gray-500">{{ number_format($product->total_revenue) }} تومان</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">هنوز فروشی ثبت نشده است</p>
            @endif
        </div>

        <!-- Top Categories -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">دسته‌بندی‌های پرفروش</h3>
            @if($topCategories->count() > 0)
                <div class="space-y-4">
                    @foreach($topCategories as $category)
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900">{{ $category->name }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-blue-600">{{ number_format($category->total_sold) }} فروش</p>
                                <p class="text-sm text-gray-500">{{ number_format($category->total_revenue) }} تومان</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">هنوز فروشی ثبت نشده است</p>
            @endif
        </div>
    </div>

    <!-- Alerts & Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Stock Alerts -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">هشدارهای موجودی</h3>
            @if($stats['low_stock_products'] > 0)
                <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm text-yellow-800">
                        <span class="font-medium">{{ $stats['low_stock_products'] }}</span> محصول موجودی کم دارند
                    </p>
                </div>
            @endif
            @if($stats['out_of_stock_products'] > 0)
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-800">
                        <span class="font-medium">{{ $stats['out_of_stock_products'] }}</span> محصول ناموجود هستند
                    </p>
                </div>
            @endif
            
            @if($stats['low_stock_items']->count() > 0)
                <div class="space-y-2">
                    <h4 class="font-medium text-gray-700">محصولات کم موجودی:</h4>
                    @foreach($stats['low_stock_items'] as $item)
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600">{{ $item->name }}</span>
                            <span class="text-red-600 font-medium">{{ $item->stock }} عدد</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Recent Orders -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">سفارشات اخیر</h3>
            <div class="space-y-3">
                @foreach($stats['recent_orders']->take(5) as $order)
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm font-medium text-gray-900">#{{ $order->id }}</p>
                            <p class="text-xs text-gray-500">{{ $order->user->name }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium">{{ number_format($order->total_amount) }} تومان</p>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                @if($order->status === 'completed') bg-green-100 text-green-800
                                @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($order->status === 'shipped') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                @switch($order->status)
                                    @case('pending') در انتظار @break
                                    @case('paid') پرداخت شده @break
                                    @case('shipped') ارسال شده @break
                                    @case('completed') تکمیل شده @break
                                    @case('canceled') لغو شده @break
                                    @default {{ $order->status }}
                                @endswitch
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Users -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">کاربران جدید</h3>
            <div class="space-y-3">
                @foreach($stats['recent_users'] as $user)
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-8 w-8">
                            <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                <span class="text-sm font-medium text-gray-700">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                        </div>
                        <div class="mr-3">
                            <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Sales Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: @json($salesChart->pluck('label')),
            datasets: [{
                label: 'فروش (تومان)',
                data: @json($salesChart->pluck('sales')),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Orders Chart
    const ordersCtx = document.getElementById('ordersChart').getContext('2d');
    const ordersChart = new Chart(ordersCtx, {
        type: 'bar',
        data: {
            labels: @json($ordersChart->pluck('label')),
            datasets: [{
                label: 'تعداد سفارشات',
                data: @json($ordersChart->pluck('orders')),
                backgroundColor: 'rgba(34, 197, 94, 0.8)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
