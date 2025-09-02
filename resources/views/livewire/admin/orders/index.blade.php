<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-900">مدیریت سفارشات</h2>
            <a href="{{ route('admin.orders.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                ایجاد سفارش جدید
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">جستجو</label>
                <input type="text" wire:model.live="search" placeholder="شماره سفارش یا نام مشتری..." 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">وضعیت</label>
                <select wire:model.live="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">همه وضعیت‌ها</option>
                    <option value="pending">در انتظار</option>
                    <option value="processing">در حال پردازش</option>
                    <option value="shipped">ارسال شده</option>
                    <option value="delivered">تحویل داده شده</option>
                    <option value="cancelled">لغو شده</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">تعداد در صفحه</label>
                <select wire:model.live="perPage" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">شماره سفارش</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">مشتری</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">مبلغ کل</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">وضعیت</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاریخ سفارش</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">عملیات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $order->order_number ?? '#'.$order->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $order->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $order->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($order->total_amount) }} تومان</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <select wire:change="updateOrderStatus({{ $order->id }}, $event.target.value)" 
                                        class="text-xs font-semibold rounded-full px-2 py-1 border-0
                                        @if($order->status === 'delivered') bg-green-100 text-green-800
                                        @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->status === 'shipped') bg-blue-100 text-blue-800
                                        @elseif($order->status === 'processing') bg-indigo-100 text-indigo-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>در انتظار</option>
                                    <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>در حال پردازش</option>
                                    <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>ارسال شده</option>
                                    <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>تحویل داده شده</option>
                                     <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>لغو شده</option>
                                </select>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->created_at->format('Y/m/d H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2 space-x-reverse">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:text-blue-900">مشاهده</a>
                                    <a href="{{ route('admin.orders.edit', $order) }}" class="text-indigo-600 hover:text-indigo-900">ویرایش</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">هیچ سفارشی یافت نشد</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $orders->links() }}
        </div>
    </div>
</div>
