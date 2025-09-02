<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-900">جزئیات سفارش #{{ $order->id }}</h2>
            <div class="flex space-x-2 space-x-reverse">
                <button wire:click="printOrder" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                    چاپ سفارش
                </button>
                <a href="{{ route('admin.orders.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                    بازگشت به لیست
                </a>
            </div>
        </div>
    </div>

    <!-- Order Info Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Customer Info -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">اطلاعات مشتری</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700">نام مشتری</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $order->user->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">ایمیل</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $order->user->email }}</p>
                </div>
                @if($order->user->phone)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">تلفن</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $order->user->phone }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Order Status -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">وضعیت سفارش</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">وضعیت فعلی</label>
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                        @if($order->status === 'completed') bg-green-100 text-green-800
                        @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($order->status === 'shipped') bg-blue-100 text-blue-800
                        @elseif($order->status === 'paid') bg-indigo-100 text-indigo-800
                        @else bg-red-100 text-red-800
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
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">تغییر وضعیت</label>
                    <select wire:change="updateStatus($event.target.value)" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>در انتظار</option>
                        <option value="paid" {{ $order->status === 'paid' ? 'selected' : '' }}>پرداخت شده</option>
                        <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>ارسال شده</option>
                        <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>تکمیل شده</option>
                        <option value="canceled" {{ $order->status === 'canceled' ? 'selected' : '' }}>لغو شده</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">خلاصه سفارش</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">تاریخ سفارش:</span>
                    <span class="text-sm font-medium">{{ $order->created_at->format('Y/m/d H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">تعداد آیتم:</span>
                    <span class="text-sm font-medium">{{ $order->orderItems->count() }} قلم</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">مجموع تعداد:</span>
                    <span class="text-sm font-medium">{{ $order->orderItems->sum('quantity') }} عدد</span>
                </div>
                <div class="border-t pt-3">
                    <div class="flex justify-between">
                        <span class="text-base font-medium text-gray-900">مبلغ کل:</span>
                        <span class="text-base font-bold text-gray-900">{{ number_format($order->total_amount) }} تومان</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Items -->
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">آیتم‌های سفارش</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">محصول</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">قیمت واحد</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تعداد</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">مجموع</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">وضعیت محصول</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($order->orderItems as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded bg-gray-100 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    </div>
                                    <div class="mr-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                        <div class="text-sm text-gray-500">SKU: {{ $item->product->sku }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($item->price) }} تومان</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ number_format($item->price * $item->quantity) }} تومان</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    @if($item->product->status === 'active') bg-green-100 text-green-800
                                    @elseif($item->product->status === 'draft') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    @switch($item->product->status)
                                        @case('active') فعال @break
                                        @case('draft') پیش‌نویس @break
                                        @case('out_of_stock') ناموجود @break
                                        @default {{ $item->product->status }}
                                    @endswitch
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900">مجموع کل:</td>
                        <td class="px-6 py-4 text-sm font-bold text-gray-900">{{ number_format($order->total_amount) }} تومان</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Payment Info -->
    @if($order->payment)
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">اطلاعات پرداخت</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">روش پرداخت</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $order->payment->payment_method }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">وضعیت پرداخت</label>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                            @if($order->payment->status === 'completed') bg-green-100 text-green-800
                            @elseif($order->payment->status === 'pending') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ $order->payment->status }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">مبلغ پرداخت</label>
                        <p class="mt-1 text-sm text-gray-900">{{ number_format($order->payment->amount) }} تومان</p>
                    </div>
                    @if($order->payment->transaction_id)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">شماره تراکنش</label>
                            <p class="mt-1 text-sm text-gray-900 font-mono">{{ $order->payment->transaction_id }}</p>
                        </div>
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-gray-700">تاریخ پرداخت</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $order->payment->created_at->format('Y/m/d H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Shipping Address -->
    @if($order->shipping_address)
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">آدرس ارسال</h3>
            </div>
            <div class="p-6">
                <p class="text-sm text-gray-900">{{ $order->shipping_address }}</p>
            </div>
        </div>
    @endif
</div>
