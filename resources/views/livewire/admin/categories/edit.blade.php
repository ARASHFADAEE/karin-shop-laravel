<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-900">ویرایش دسته‌بندی: {{ $category->name }}</h2>
            <a href="{{ route('admin.categories.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                بازگشت به لیست
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">ویرایش اطلاعات دسته‌بندی</h3>
            <p class="text-sm text-gray-600 mt-1">شناسه دسته‌بندی: #{{ $category->id }}</p>
        </div>
        
        <form wire:submit="save" class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Name -->
                <div class="lg:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">نام دسته‌بندی *</label>
                    <input type="text" 
                           id="name"
                           wire:model="name" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="نام دسته‌بندی را وارد کنید">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Parent Category -->
                <div>
                    <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-2">دسته والد</label>
                    <select wire:model="parent_id" 
                            id="parent_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">دسته اصلی (بدون والد)</option>
                        @foreach($parentCategories as $parentCategory)
                            <option value="{{ $parentCategory->id }}">{{ $parentCategory->name }}</option>
                        @endforeach
                    </select>
                    @error('parent_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Slug Generation -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">تولید اسلاگ</label>
                    <div class="flex">
                        <div class="flex-1 border border-gray-200 rounded-r-lg px-3 py-2 bg-gray-50 text-gray-600 font-mono text-sm">
                            @if($name)
                                {{ Str::slug($name) }}
                            @else
                                {{ $category->slug }}
                            @endif
                        </div>
                        <button type="button" 
                                wire:click="generateSlug"
                                wire:loading.attr="disabled"
                                wire:target="generateSlug"
                                class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white px-4 py-2 rounded-l-lg border border-blue-600">
                            <span wire:loading.remove wire:target="generateSlug">تولید اسلاگ</span>
                            <span wire:loading wire:target="generateSlug">در حال تولید...</span>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">اسلاگ از ترجمه انگلیسی نام دسته‌بندی تولید می‌شود</p>
                </div>

                <!-- Description -->
                <div class="lg:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">توضیحات</label>
                    <textarea wire:model="description" 
                              id="description"
                              rows="4"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="توضیحات دسته‌بندی را وارد کنید..."></textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Category Info -->
            <div class="mt-6 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-700">تاریخ ایجاد:</span>
                        <span class="text-gray-600">{{ $category->created_at->format('Y/m/d H:i') }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">آخرین به‌روزرسانی:</span>
                        <span class="text-gray-600">{{ $category->updated_at->format('Y/m/d H:i') }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Slug فعلی:</span>
                        <span class="text-gray-600 font-mono">{{ $category->slug }}</span>
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
                            <li>تغییر نام دسته‌بندی بر URL های مرتبط تأثیر می‌گذارد</li>
                            <li>نمی‌توانید دسته‌بندی را به عنوان والد خودش انتخاب کنید</li>
                            <li>تغییر دسته والد ممکن است بر ساختار سایت تأثیر بگذارد</li>
                            <li>Slug جدید به صورت خودکار تولید می‌شود</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="mt-6 flex justify-end space-x-3 space-x-reverse">
                <a href="{{ route('admin.categories.index') }}" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg">
                    انصراف
                </a>
                <button type="submit" 
                        wire:loading.attr="disabled"
                        class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white px-6 py-2 rounded-lg flex items-center">
                    <span wire:loading.remove>به‌روزرسانی دسته‌بندی</span>
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
