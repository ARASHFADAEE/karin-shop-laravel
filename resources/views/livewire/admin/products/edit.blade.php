<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-900">ویرایش محصول: {{ $product->name }}</h2>
            <div class="flex space-x-2 space-x-reverse">
                <a href="{{ route('admin.products.show', $product) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                    مشاهده محصول
                </a>
                <a href="{{ route('admin.products.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                    بازگشت به لیست
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">ویرایش اطلاعات محصول</h3>
            <p class="text-sm text-gray-600 mt-1">شناسه محصول: #{{ $product->id }}</p>
        </div>
        
        <form wire:submit="save" class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Name -->
                <div class="lg:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">نام محصول *</label>
                    <input type="text" 
                           id="name"
                           wire:model="name" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="نام محصول را وارد کنید">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">دسته‌بندی *</label>
                    <select wire:model="category_id" 
                            id="category_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">انتخاب دسته‌بندی</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">وضعیت *</label>
                    <select wire:model="status" 
                            id="status"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="active">فعال</option>
                        <option value="draft">پیش‌نویس</option>
                        <option value="out_of_stock">ناموجود</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price -->
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-2">قیمت (تومان) *</label>
                    <input type="number" 
                           id="price"
                           wire:model="price" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="0"
                           min="0"
                           step="0.01">
                    @error('price')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Stock -->
                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">موجودی *</label>
                    <input type="number" 
                           id="stock"
                           wire:model="stock" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="0"
                           min="0">
                    @error('stock')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- SKU -->
                <div class="lg:col-span-2">
                    <label for="sku" class="block text-sm font-medium text-gray-700 mb-2">کد محصول (SKU) *</label>
                    <div class="flex">
                        <input type="text" 
                               id="sku"
                               wire:model="sku" 
                               class="flex-1 border border-gray-300 rounded-r-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="PRD-XXXXXXXX">
                        <button type="button" 
                                wire:click="generateSku"
                                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-l-lg border border-gray-300 border-r-0">
                            تولید جدید
                        </button>
                    </div>
                    @error('sku')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="lg:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">توضیحات</label>
                    <textarea wire:model="description" 
                              id="description"
                              rows="4"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="توضیحات محصول را وارد کنید..."></textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Product Info -->
            <div class="mt-6 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-700">تاریخ ایجاد:</span>
                        <span class="text-gray-600">{{ $product->created_at->format('Y/m/d H:i') }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">آخرین به‌روزرسانی:</span>
                        <span class="text-gray-600">{{ $product->updated_at->format('Y/m/d H:i') }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Slug فعلی:</span>
                        <span class="text-gray-600">{{ $product->slug }}</span>
                    </div>
                </div>
            </div>

            <!-- Warning Box -->
            <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <div>
                        <p class="text-sm text-yellow-800 font-medium">هشدار</p>
                        <ul class="text-xs text-yellow-700 mt-1 list-disc list-inside">
                            <li>تغییر نام محصول، slug آن را نیز تغییر می‌دهد</li>
                            <li>تغییر وضعیت محصول بر نمایش آن در فروشگاه تأثیر می‌گذارد</li>
                            <li>کاهش موجودی ممکن است بر سفارشات در انتظار تأثیر بگذارد</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="mt-6 flex justify-end space-x-3 space-x-reverse">
                <a href="{{ route('admin.products.index') }}" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg">
                    انصراف
                </a>
                <button type="submit" 
                        wire:loading.attr="disabled"
                        class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white px-6 py-2 rounded-lg flex items-center">
                    <span wire:loading.remove>به‌روزرسانی محصول</span>
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
</div>
