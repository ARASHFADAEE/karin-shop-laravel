<div>
    @section('title', 'ایجاد تیکت جدید')

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold">ایجاد تیکت جدید</h2>
            <a href="{{ route('admin.tickets.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                بازگشت به لیست
            </a>
        </div>

        <form wire:submit="createTicket">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-4">
                    <!-- Subject -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">موضوع تیکت *</label>
                        <input type="text" wire:model="subject"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="موضوع تیکت را وارد کنید...">
                        @error('subject') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">توضیحات *</label>
                        <textarea wire:model="description" rows="6"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="توضیحات کامل تیکت را وارد کنید..."></textarea>
                        @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Priority -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">اولویت *</label>
                        <select wire:model="priority"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach($priorityOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('priority') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">دسته‌بندی *</label>
                        <select wire:model="category"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach($categoryOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('category') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Department -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">بخش مسئول *</label>
                        <select wire:model="department"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach($departmentOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('department') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-4">
                    <!-- User -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">کاربر</label>
                        <select wire:model="user_id"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">انتخاب کاربر...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                        @error('user_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Order -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">سفارش مرتبط</label>
                        <select wire:model="order_id"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">انتخاب سفارش...</option>
                            @foreach($orders as $order)
                                <option value="{{ $order->id }}">
                                    سفارش #{{ $order->id }} - {{ $order->user->name ?? 'نامشخص' }}
                                    ({{ number_format($order->total_amount) }} تومان)
                                </option>
                            @endforeach
                        </select>
                        @error('order_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Product -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">محصول مرتبط</label>
                        <select wire:model="product_id"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">انتخاب محصول...</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                        @error('product_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Tags -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">تگ‌ها</label>
                        <input type="text" wire:model="tags"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="تگ‌ها را با کاما جدا کنید...">
                        @error('tags') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        <p class="text-xs text-gray-500 mt-1">مثال: فوری، مهم، بررسی</p>
                    </div>

                    <!-- Preview Box -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-medium text-gray-700 mb-2">پیش‌نمایش تیکت:</h4>
                        <div class="space-y-2 text-sm">
                            <div><strong>موضوع:</strong> {{ $subject ?: 'وارد نشده' }}</div>
                            <div><strong>اولویت:</strong>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($priority === 'urgent') bg-red-100 text-red-800
                                    @elseif($priority === 'high') bg-orange-100 text-orange-800
                                    @elseif($priority === 'medium') bg-yellow-100 text-yellow-800
                                    @else bg-green-100 text-green-800 @endif">
                                    {{ $priorityOptions[$priority] ?? 'متوسط' }}
                                </span>
                            </div>
                            <div><strong>دسته‌بندی:</strong> {{ $categoryOptions[$category] ?? 'عمومی' }}</div>
                            <div><strong>بخش:</strong> {{ $departmentOptions[$department] ?? 'پشتیبانی' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 space-x-reverse mt-6 pt-6 border-t">
                <button type="button" wire:click="resetForm"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg">
                    پاک کردن فرم
                </button>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                    ایجاد تیکت
                </button>
            </div>
        </form>
    </div>
</div>
