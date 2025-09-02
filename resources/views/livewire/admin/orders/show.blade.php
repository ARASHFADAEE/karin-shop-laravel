<div>
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg" role="alert">
            <div class="flex items-center">
                <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif
    
    @if (session()->has('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg" role="alert">
            <div class="flex items-center">
                <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif
    
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-900">جزئیات سفارش {{ $order->order_number ?? '#' . $order->id }}</h2>
            <div class="flex space-x-2 space-x-reverse">
                <!-- Print Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        چاپ و دانلود
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    
                    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute left-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                        <div class="py-2">
                            <!-- Invoice Option -->
                            <button wire:click="showInvoiceHtml" class="w-full text-right px-4 py-3 hover:bg-gray-50 flex items-center">
                                <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center ml-3">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900">مشاهده فاکتور</div>
                                    <div class="text-xs text-gray-500">فاکتور کامل سفارش با امکان چاپ و دانلود</div>
                                </div>
                            </button>
                            
                            <!-- Shipping Label Option -->
                            <button wire:click="showShippingLabelHtml" class="w-full text-right px-4 py-3 hover:bg-gray-50 flex items-center">
                                <div class="flex-shrink-0 w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center ml-3">
                                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900">مشاهده برچسب ارسال</div>
                                    <div class="text-xs text-gray-500">برچسب برای چسباندن روی بسته با امکان چاپ</div>
                                </div>
                            </button>
                            

                        </div>
                    </div>
                </div>
                
                <a href="{{ route('admin.orders.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    بازگشت به لیست
                </a>
            </div>
        </div>
    </div>
    
    <!-- Print Info Banner -->
    <div class="mb-6 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="mr-3 flex-1">
                <h3 class="text-sm font-medium text-blue-800">قابلیت‌های چاپ و دانلود</h3>
                <div class="mt-1 text-sm text-blue-700">
                    <p>• <strong>فاکتور PDF:</strong> فاکتور کامل سفارش با تمام جزئیات برای ارسال به مشتری</p>
                    <p>• <strong>برچسب ارسال:</strong> برچسب حرفه‌ای برای چسباندن روی بسته (ابعاد 100×150 میلی‌متر)</p>
                </div>
            </div>
            <div class="flex-shrink-0">
                <div class="flex space-x-2 space-x-reverse">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        PDF آماده
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        قابل چاپ
                    </span>
                </div>
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
                        @if($order->status === 'delivered') bg-green-100 text-green-800
                        @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($order->status === 'shipped') bg-blue-100 text-blue-800
                        @elseif($order->status === 'processing') bg-indigo-100 text-indigo-800
                        @else bg-red-100 text-red-800
                        @endif">
                        @switch($order->status)
                            @case('pending') در انتظار @break
                            @case('processing') در حال پردازش @break
                            @case('shipped') ارسال شده @break
                            @case('delivered') تحویل داده شده @break
                            @case('cancelled') لغو شده @break
                            @default {{ $order->status }}
                        @endswitch
                    </span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">تغییر وضعیت</label>
                    <select wire:change="updateStatus($event.target.value)" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>در انتظار</option>
                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>در حال پردازش</option>
                        <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>ارسال شده</option>
                        <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>تحویل داده شده</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>لغو شده</option>
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
