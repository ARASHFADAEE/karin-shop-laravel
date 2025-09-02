<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-900">ویرایش سفارش #{{ $order->id }}</h2>
            <div class="flex space-x-2 space-x-reverse">
                <a href="{{ route('admin.orders.show', $order) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                    مشاهده سفارش
                </a>
                <a href="{{ route('admin.orders.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                    بازگشت به لیست
                </a>
            </div>
        </div>
    </div>

    <form wire:submit="save">
        <!-- Order Info -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">اطلاعات سفارش</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Customer Search -->
                <div>
                    <label for="searchUser" class="block text-sm font-medium text-gray-700 mb-2">جستجوی مشتری *</label>
                    <div class="relative">
                        <input type="text" 
                               wire:model.live="searchUser"
                               id="searchUser"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="نام، ایمیل یا شماره تلفن مشتری را وارد کنید...">
                        
                        @if($selectedUserName)
                            <div class="mt-2 p-2 bg-green-50 border border-green-200 rounded-lg">
                                <span class="text-green-800 text-sm">مشتری انتخاب شده: {{ $selectedUserName }}</span>
                                <button type="button" wire:click="$set('user_id', '')" wire:click="$set('selectedUserName', '')" class="mr-2 text-red-600 hover:text-red-800">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        @endif
                        
                        @if(!empty($userSearchResults))
                            <div class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                @foreach($userSearchResults as $user)
                                    <div wire:click="selectUser({{ $user['id'] }}, '{{ $user['name'] }}')"
                                         class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0">
                                        <div class="font-medium text-gray-900">{{ $user['name'] }}</div>
                                        <div class="text-sm text-gray-600">{{ $user['email'] }}</div>
                                        @if($user['phone'])
                                            <div class="text-sm text-gray-500">{{ $user['phone'] }}</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    @error('user_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Order Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">وضعیت سفارش *</label>
                    <select wire:model="status" 
                            id="status"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="pending">در انتظار</option>
                        <option value="paid">پرداخت شده</option>
                        <option value="shipped">ارسال شده</option>
                        <option value="completed">تکمیل شده</option>
                        <option value="canceled">لغو شده</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Payment Method -->
                <div>
                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">روش پرداخت *</label>
                    <select wire:model="payment_method" 
                            id="payment_method"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="cash">نقدی</option>
                        <option value="card">کارت</option>
                        <option value="online">آنلاین</option>
                        <option value="bank_transfer">انتقال بانکی</option>
                    </select>
                    @error('payment_method')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- User Addresses -->
                @if(!empty($userAddresses))
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">آدرس‌های ثبت شده</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            @foreach($userAddresses as $address)
                                <div wire:click="selectAddress({{ $address['id'] }})"
                                     class="p-4 border rounded-lg cursor-pointer transition-colors {{ $selected_address_id == $address['id'] ? 'border-blue-500 bg-blue-50' : 'border-gray-300 hover:border-gray-400' }}">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="font-medium text-gray-900">{{ $address['title'] }}</span>
                                        @if($address['is_default'])
                                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">پیش‌فرض</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600">{{ $address['address'] }}</p>
                                    <p class="text-sm text-gray-500">{{ $address['city'] }}, {{ $address['state'] }}</p>
                                    @if($address['phone'])
                                        <p class="text-sm text-gray-500">تلفن: {{ $address['phone'] }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Shipping Address -->
                <div class="md:col-span-2">
                    <label for="shipping_address" class="block text-sm font-medium text-gray-700 mb-2">آدرس ارسال</label>
                    <textarea wire:model="shipping_address" 
                              id="shipping_address"
                              rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="آدرس کامل ارسال سفارش..."></textarea>
                    @error('shipping_address')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">می‌توانید از آدرس‌های بالا انتخاب کنید یا آدرس جدید وارد کنید</p>
                </div>
            </div>
        </div>

        <!-- Product Search -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">مدیریت محصولات</h3>
            <div class="relative">
                <input type="text" 
                       wire:model.live="searchProduct"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="جستجو محصول بر اساس نام یا SKU...">
                
                @if(!empty($searchResults))
                    <div class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                        @foreach($searchResults as $product)
                            <div wire:click="addProduct({{ $product['id'] }})"
                                 class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $product['name'] }}</div>
                                        <div class="text-sm text-gray-600">SKU: {{ $product['sku'] }}</div>
                                    </div>
                                    <div class="text-left">
                                        <div class="text-sm font-medium text-green-600">{{ number_format($product['price']) }} تومان</div>
                                        <div class="text-xs text-gray-500">موجودی: {{ $product['stock'] }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Order Items -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">اقلام سفارش</h3>
            
            @if(!empty($orderItems) && !empty(array_filter($orderItems, fn($item) => !empty($item['product_id']))))
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">محصول</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تعداد</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">قیمت واحد</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">مجموع</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">عملیات</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($orderItems as $index => $item)
                                @if(!empty($item['product_id']))
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $item['product_name'] }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" 
                                                   wire:change="updateQuantity({{ $index }}, $event.target.value)"
                                                   value="{{ $item['quantity'] }}"
                                                   min="1"
                                                   class="w-20 border border-gray-300 rounded px-2 py-1 text-sm">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" 
                                                   wire:change="updatePrice({{ $index }}, $event.target.value)"
                                                   value="{{ $item['price'] }}"
                                                   min="0"
                                                   step="0.01"
                                                   class="w-24 border border-gray-300 rounded px-2 py-1 text-sm">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ number_format($item['quantity'] * $item['price']) }} تومان
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <button type="button" 
                                                    wire:click="removeProduct({{ $index }})"
                                                    class="text-red-600 hover:text-red-900">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900">مجموع کل:</td>
                                <td class="px-6 py-4 text-sm font-bold text-gray-900">{{ number_format($this->calculateTotal()) }} تومان</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m13-8V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v1M7 8h10M7 12h4m1 8l-1-4H9l-1 4h4z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">هیچ محصولی انتخاب نشده</h3>
                    <p class="mt-1 text-sm text-gray-500">برای افزودن محصول از قسمت جستجو استفاده کنید.</p>
                </div>
            @endif
        </div>

        <!-- Submit Buttons -->
        <div class="bg-white shadow rounded-lg">
            <div class="p-6">
                <div class="flex justify-end space-x-3 space-x-reverse">
                    <a href="{{ route('admin.orders.index') }}" 
                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg">
                        انصراف
                    </a>
                    <button type="submit" 
                            wire:loading.attr="disabled"
                            class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white px-6 py-2 rounded-lg flex items-center">
                        <span wire:loading.remove>به‌روزرسانی سفارش</span>
                        <span wire:loading class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            در حال به‌روزرسانی...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Info Box -->
    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-blue-600 mt-0.5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <p class="text-sm text-blue-800 font-medium">نکات مهم</p>
                <ul class="text-xs text-blue-700 mt-1 list-disc list-inside">
                    <li>تغییر اطلاعات سفارش بر وضعیت پرداخت و ارسال تأثیر می‌گذارد</li>
                    <li>حذف یا اضافه کردن محصولات موجودی انبار را تغییر می‌دهد</li>
                    <li>تغییر مشتری سفارش تاریخچه خرید را تحت تأثیر قرار می‌دهد</li>
                    <li>آدرس ارسال از آدرس‌های ثبت شده مشتری قابل انتخاب است</li>
                </ul>
            </div>
        </div>
    </div>
</div>
