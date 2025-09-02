<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-900">ویرایش کوپن: {{ $coupon->code }}</h2>
            <a href="{{ route('admin.coupons.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                بازگشت به لیست
            </a>
        </div>
    </div>

    <!-- Coupon Info -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-blue-600 mt-0.5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <p class="text-sm text-blue-800 font-medium">اطلاعات کوپن</p>
                <div class="text-xs text-blue-700 mt-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>شناسه: #{{ $coupon->id }}</div>
                    <div>تاریخ ایجاد: {{ $coupon->created_at->format('Y/m/d H:i') }}</div>
                    <div>آخرین بروزرسانی: {{ $coupon->updated_at->format('Y/m/d H:i') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">ویرایش اطلاعات کوپن</h3>
        </div>
        
        <form wire:submit="save" class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Code -->
                <div class="lg:col-span-2">
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">کد کوپن *</label>
                    <input type="text" 
                           id="code"
                           wire:model="code" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="مثال: SUMMER2024">
                    @error('code')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">کد کوپن باید یکتا باشد و حداکثر 50 کاراکتر</p>
                </div>

                <!-- Description -->
                <div class="lg:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">توضیحات</label>
                    <textarea wire:model="description" 
                              id="description"
                              rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="توضیحات کوپن تخفیف..."></textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">نوع تخفیف *</label>
                    <select wire:model.live="type" 
                            id="type"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="percentage">درصدی</option>
                        <option value="fixed">مبلغ ثابت</option>
                    </select>
                    @error('type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Value -->
                <div>
                    <label for="value" class="block text-sm font-medium text-gray-700 mb-2">
                        مقدار تخفیف *
                        @if($type === 'percentage')
                            (درصد)
                        @else
                            (تومان)
                        @endif
                    </label>
                    <input type="number" 
                           id="value"
                           wire:model="value" 
                           min="0"
                           @if($type === 'percentage') max="100" @endif
                           step="@if($type === 'percentage') 1 @else 1000 @endif"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="@if($type === 'percentage') مثال: 20 @else مثال: 50000 @endif">
                    @error('value')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    @if($type === 'percentage')
                        <p class="text-xs text-gray-500 mt-1">درصد تخفیف بین 0 تا 100</p>
                    @else
                        <p class="text-xs text-gray-500 mt-1">مبلغ تخفیف به تومان</p>
                    @endif
                </div>

                <!-- Minimum Amount -->
                <div>
                    <label for="minimum_amount" class="block text-sm font-medium text-gray-700 mb-2">حداقل مبلغ خرید (تومان)</label>
                    <input type="number" 
                           id="minimum_amount"
                           wire:model="minimum_amount" 
                           min="0"
                           step="1000"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="مثال: 100000">
                    @error('minimum_amount')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">خالی بگذارید اگر محدودیتی ندارد</p>
                </div>

                <!-- Usage Limit -->
                <div>
                    <label for="usage_limit" class="block text-sm font-medium text-gray-700 mb-2">محدودیت استفاده</label>
                    <input type="number" 
                           id="usage_limit"
                           wire:model="usage_limit" 
                           min="1"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="مثال: 100">
                    @error('usage_limit')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">تعداد دفعات قابل استفاده (خالی = نامحدود)</p>
                </div>

                <!-- Expires At -->
                <div>
                    <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-2">تاریخ انقضا</label>
                    <input type="datetime-local" 
                           id="expires_at"
                           wire:model="expires_at" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('expires_at')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">خالی بگذارید اگر تاریخ انقضا ندارد</p>
                </div>

                <!-- Is Active -->
                <div class="lg:col-span-2">
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="is_active"
                               wire:model="is_active" 
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <label for="is_active" class="mr-2 text-sm text-gray-700">کوپن فعال باشد</label>
                    </div>
                    @error('is_active')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">کوپن‌های غیرفعال قابل استفاده نیستند</p>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="mt-6 flex justify-end space-x-3 space-x-reverse">
                <a href="{{ route('admin.coupons.index') }}" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg">
                    انصراف
                </a>
                <button type="submit" 
                        wire:loading.attr="disabled"
                        class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white px-6 py-2 rounded-lg flex items-center">
                    <span wire:loading.remove>به‌روزرسانی کوپن</span>
                    <span wire:loading class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        در حال به‌روزرسانی...
                    </span>
                </button>
            </div>
        </form>
    </div>

    <!-- Usage Statistics -->
    @if($coupon->usage_count > 0)
        <div class="mt-6 bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">آمار استفاده</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="text-sm font-medium text-gray-500">تعداد استفاده</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $coupon->usage_count ?? 0 }}</div>
                </div>
                @if($coupon->usage_limit)
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="text-sm font-medium text-gray-500">باقی‌مانده</div>
                        <div class="text-2xl font-bold text-blue-600">{{ $coupon->usage_limit - ($coupon->usage_count ?? 0) }}</div>
                    </div>
                @endif
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="text-sm font-medium text-gray-500">وضعیت</div>
                    <div class="text-lg font-medium {{ $coupon->is_active ? 'text-green-600' : 'text-red-600' }}">
                        {{ $coupon->is_active ? 'فعال' : 'غیرفعال' }}
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Warning Box -->
    <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-yellow-600 mt-0.5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <div>
                <p class="text-sm text-yellow-800 font-medium">نکات مهم</p>
                <ul class="text-xs text-yellow-700 mt-1 list-disc list-inside">
                    <li>تغییر کد کوپن ممکن است بر لینک‌های اشتراک‌گذاری شده تأثیر بگذارد</li>
                    <li>کاهش مقدار تخفیف بر سفارشات در حال انتظار تأثیر نمی‌گذارد</li>
                    <li>غیرفعال کردن کوپن استفاده از آن را متوقف می‌کند</li>
                    <li>تغییر تاریخ انقضا بر کاربرانی که قبلاً کوپن را دریافت کرده‌اند تأثیر می‌گذارد</li>
                </ul>
            </div>
        </div>
    </div>
</div>
