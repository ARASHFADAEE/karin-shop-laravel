<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-900">افزودن محصول جدید</h2>
            <a href="{{ route('admin.products.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                بازگشت به لیست
            </a>
        </div>
    </div>

    <form wire:submit="save">
        <!-- Basic Information -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">اطلاعات اصلی محصول</h3>
            </div>

            <div class="p-6">
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

                    <!-- Slug -->
                    <div class="lg:col-span-2">
                        <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">اسلاگ (URL)</label>
                        <div class="flex">
                            <input type="text"
                                   id="slug"
                                   wire:model="slug"
                                   class="flex-1 border border-gray-300 rounded-r-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="اسلاگ محصول">
                            <button type="button"
                                    wire:click="generateSlug"
                                    wire:loading.attr="disabled"
                                    wire:target="generateSlug"
                                    class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white px-4 py-2 rounded-l-lg border border-blue-600">
                                <span wire:loading.remove wire:target="generateSlug">تولید اسلاگ</span>
                                <span wire:loading wire:target="generateSlug">در حال تولید...</span>
                            </button>
                        </div>
                        @error('slug')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">اسلاگ از ترجمه انگلیسی نام محصول تولید می‌شود</p>
                    </div>

                    <!-- Categories (Multi-select with Search) -->
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">دسته‌بندی‌ها *</label>

                        <!-- Category Search and Selection -->
                        <div class="border border-gray-300 rounded-lg p-4 bg-gray-50">
                            <!-- Search Input -->
                            <div class="mb-3">
                                <input type="text"
                                       wire:model.live="categorySearch"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="جستجو در دسته‌بندی‌ها...">
                            </div>

                            <!-- Categories List (Scrollable) -->
                            <div class="max-h-48 overflow-y-auto border border-gray-200 rounded-lg bg-white">
                                @php
                                    $filteredCategories = $categories;
                                    if (!empty($categorySearch)) {
                                        $filteredCategories = $categories->filter(function($category) {
                                            return str_contains(strtolower($category->name), strtolower($this->categorySearch ?? ''));
                                        });
                                    }
                                @endphp

                                @if($filteredCategories->count() > 0)
                                    @foreach($filteredCategories as $category)
                                        <label class="flex items-center p-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0 cursor-pointer">
                                            <input type="checkbox"
                                                   wire:model="selectedCategories"
                                                   value="{{ $category->id }}"
                                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                            <span class="mr-3 text-sm text-gray-700">{{ $category->name }}</span>
                                            @if($category->parent_id)
                                                <span class="text-xs text-gray-500 mr-auto">زیرمجموعه</span>
                                            @endif
                                        </label>
                                    @endforeach
                                @else
                                    <div class="p-4 text-center text-gray-500 text-sm">
                                        دسته‌بندی‌ای یافت نشد
                                    </div>
                                @endif
                            </div>

                            <!-- Selected Categories Display -->
                            @if(!empty($selectedCategories))
                                <div class="mt-3">
                                    <p class="text-sm font-medium text-gray-700 mb-2">دسته‌های انتخاب شده:</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($categories->whereIn('id', $selectedCategories) as $category)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $category->name }}
                                                <button type="button"
                                                        wire:click="removeCategory({{ $category->id }})"
                                                        class="mr-2 text-blue-600 hover:text-blue-800">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Primary Category Selection -->
                        @if(!empty($selectedCategories))
                            <div class="mt-4">
                                <label for="primaryCategory" class="block text-sm font-medium text-gray-700 mb-2">دسته اصلی *</label>
                                <select wire:model="primaryCategory"
                                        id="primaryCategory"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">انتخاب دسته اصلی</option>
                                    @foreach($categories->whereIn('id', $selectedCategories) as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">دسته اصلی در URL محصول و breadcrumb استفاده می‌شود</p>
                            </div>
                        @endif
                    </div>



                    <!-- Stock -->
                    <div>
                        <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">موجودی *</label>
                        <input type="number"
                               id="stock"
                               wire:model="stock"
                               min="0"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="0">
                        @error('stock')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Discount Settings -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">تنظیمات تخفیف</h3>
                <p class="text-sm text-gray-600 mt-1">تنظیم تخفیف برای محصول</p>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Has Discount -->
                    <div class="lg:col-span-2">
                        <div class="flex items-center">
                            <input type="checkbox"
                                   id="has_discount"
                                   wire:model.live="has_discount"
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <label for="has_discount" class="mr-2 text-sm font-medium text-gray-700">این محصول تخفیف دارد</label>
                        </div>
                        @error('has_discount')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    @if($has_discount)
                        <!-- Original Price -->
                        <div>
                            <label for="original_price" class="block text-sm font-medium text-gray-700 mb-2">قیمت اصلی (تومان)</label>
                            <input type="number"
                                   id="original_price"
                                   wire:model="original_price"
                                   min="0"
                                   step="1000"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="قیمت قبل از تخفیف">
                            @error('original_price')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">قیمت اصلی محصول قبل از اعمال تخفیف</p>
                        </div>

                        <!-- Current Price (After Discount) -->
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-2">قیمت با تخفیف (تومان) *</label>
                            <input type="number"
                                   id="price"
                                   wire:model="price"
                                   min="0"
                                   step="1000"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="قیمت نهایی محصول">
                            @error('price')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">قیمت نهایی که مشتری پرداخت می‌کند</p>
                        </div>

                        <!-- Discount Percentage -->
                        <div>
                            <label for="discount_percentage" class="block text-sm font-medium text-gray-700 mb-2">درصد تخفیف</label>
                            <input type="number"
                                   id="discount_percentage"
                                   wire:model="discount_percentage"
                                   min="0"
                                   max="100"
                                   step="0.01"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="مثال: 20">
                            @error('discount_percentage')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">درصد تخفیف (0 تا 100)</p>
                        </div>

                        <!-- Discount Amount -->
                        <div>
                            <label for="discount_amount" class="block text-sm font-medium text-gray-700 mb-2">مبلغ تخفیف (تومان)</label>
                            <input type="number"
                                   id="discount_amount"
                                   wire:model="discount_amount"
                                   min="0"
                                   step="1000"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="مثال: 50000">
                            @error('discount_amount')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">مبلغ ثابت تخفیف</p>
                        </div>

                        <!-- Discount Start Date -->
                        <div>
                            <label for="discount_starts_at" class="block text-sm font-medium text-gray-700 mb-2">شروع تخفیف</label>
                            <input type="datetime-local"
                                   id="discount_starts_at"
                                   wire:model="discount_starts_at"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('discount_starts_at')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">خالی بگذارید برای شروع فوری</p>
                        </div>

                        <!-- Discount End Date -->
                        <div>
                            <label for="discount_ends_at" class="block text-sm font-medium text-gray-700 mb-2">پایان تخفیف</label>
                            <input type="datetime-local"
                                   id="discount_ends_at"
                                   wire:model="discount_ends_at"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('discount_ends_at')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">خالی بگذارید برای تخفیف دائمی</p>
                        </div>
                    @else
                        <!-- Regular Price -->
                        <div class="lg:col-span-2">
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-2">قیمت (تومان) *</label>
                            <input type="number"
                                   id="price"
                                   wire:model="price"
                                   min="0"
                                   step="1000"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="قیمت محصول">
                            @error('price')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                </div>

                    <!-- SKU -->
                    <div>
                        <label for="sku" class="block text-sm font-medium text-gray-700 mb-2">کد محصول (SKU) *</label>
                        <div class="flex">
                            <input type="text"
                                   id="sku"
                                   wire:model="sku"
                                   class="flex-1 border border-gray-300 rounded-r-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="کد محصول">
                            <button type="button"
                                    wire:click="generateSku"
                                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-l-lg border border-gray-600">
                                تولید کد
                            </button>
                        </div>
                        @error('sku')
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
            </div>
        </div>

        <!-- SEO Settings -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">تنظیمات SEO</h3>
                <p class="text-sm text-gray-600 mt-1">بهینه‌سازی محصول برای موتورهای جستجو</p>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Meta Title -->
                    <div class="lg:col-span-2">
                        <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">عنوان متا (Meta Title)</label>
                        <input type="text"
                               id="meta_title"
                               wire:model="meta_title"
                               maxlength="255"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="عنوان صفحه در نتایج جستجو">
                        @error('meta_title')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">حداکثر 255 کاراکتر - در صورت خالی بودن از نام محصول استفاده می‌شود</p>
                    </div>

                    <!-- Meta Description -->
                    <div class="lg:col-span-2">
                        <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">توضیحات متا (Meta Description)</label>
                        <textarea wire:model="meta_description"
                                  id="meta_description"
                                  rows="3"
                                  maxlength="160"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="توضیح کوتاه محصول برای نمایش در نتایج جستجو"></textarea>
                        @error('meta_description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">حداکثر 160 کاراکتر - در صورت خالی بودن از توضیحات محصول استفاده می‌شود</p>
                    </div>

                    <!-- Meta Keywords -->
                    <div class="lg:col-span-2">
                        <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-2">کلمات کلیدی (Meta Keywords)</label>
                        <input type="text"
                               id="meta_keywords"
                               wire:model="meta_keywords"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="کلمات کلیدی را با کاما جدا کنید">
                        @error('meta_keywords')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">کلمات کلیدی مرتبط با محصول، با کاما جدا شده</p>
                    </div>

                    <!-- Open Graph Title -->
                    <div>
                        <label for="og_title" class="block text-sm font-medium text-gray-700 mb-2">عنوان شبکه‌های اجتماعی (OG Title)</label>
                        <input type="text"
                               id="og_title"
                               wire:model="og_title"
                               maxlength="255"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="عنوان برای اشتراک‌گذاری در شبکه‌های اجتماعی">
                        @error('og_title')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Open Graph Image -->
                    <div>
                        <label for="og_image" class="block text-sm font-medium text-gray-700 mb-2">تصویر شبکه‌های اجتماعی (OG Image)</label>
                        <input type="url"
                               id="og_image"
                               wire:model="og_image"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="https://example.com/image.jpg">
                        @error('og_image')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Open Graph Description -->
                    <div class="lg:col-span-2">
                        <label for="og_description" class="block text-sm font-medium text-gray-700 mb-2">توضیحات شبکه‌های اجتماعی (OG Description)</label>
                        <textarea wire:model="og_description"
                                  id="og_description"
                                  rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="توضیحات برای اشتراک‌گذاری در شبکه‌های اجتماعی"></textarea>
                        @error('og_description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Images -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">تصاویر محصول</h3>
                <p class="text-sm text-gray-600 mt-1">آپلود تصاویر محصول و تصاویر شاخص</p>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Regular Images -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">تصاویر عادی</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                            <input type="file"
                                   wire:model="images"
                                   multiple
                                   accept="image/*"
                                   class="hidden"
                                   id="regular-images">
                            <label for="regular-images" class="cursor-pointer">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="mt-4">
                                    <p class="text-sm text-gray-600">کلیک کنید یا فایل‌ها را اینجا بکشید</p>
                                    <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF تا 2MB</p>
                                </div>
                            </label>
                        </div>
                        @error('images.*')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror

                        @if($images)
                            <div class="mt-3">
                                <p class="text-sm text-gray-600">{{ count($images) }} فایل انتخاب شده</p>
                            </div>
                        @endif
                    </div>

                    <!-- Featured Images -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">تصاویر شاخص</label>
                        <div class="border-2 border-dashed border-yellow-300 rounded-lg p-6 text-center hover:border-yellow-400 transition-colors">
                            <input type="file"
                                   wire:model="featuredImages"
                                   multiple
                                   accept="image/*"
                                   class="hidden"
                                   id="featured-images">
                            <label for="featured-images" class="cursor-pointer">
                                <svg class="mx-auto h-12 w-12 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                <div class="mt-4">
                                    <p class="text-sm text-gray-600">تصاویر شاخص محصول</p>
                                    <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF تا 2MB</p>
                                </div>
                            </label>
                        </div>
                        @error('featuredImages.*')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror

                        @if($featuredImages)
                            <div class="mt-3">
                                <p class="text-sm text-gray-600">{{ count($featuredImages) }} فایل انتخاب شده</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Attributes -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">ویژگی‌های محصول</h3>
                <p class="text-sm text-gray-600 mt-1">مشخصات فنی و ویژگی‌های محصول</p>
            </div>

            <div class="p-6">
                @foreach($productAttributes as $index => $attribute)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 p-4 border border-gray-200 rounded-lg">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">نام ویژگی</label>
                            <input type="text"
                                   wire:model="productAttributes.{{ $index }}.name"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="مثال: رنگ، سایز، جنس">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">مقدار ویژگی</label>
                            <div class="flex">
                                <input type="text"
                                       wire:model="productAttributes.{{ $index }}.value"
                                       class="flex-1 border border-gray-300 rounded-r-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="مثال: قرمز، XL، پنبه">
                                @if(count($productAttributes) > 1)
                                    <button type="button"
                                            wire:click="removeAttribute({{ $index }})"
                                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-l-lg">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                <button type="button"
                        wire:click="addAttribute"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    افزودن ویژگی جدید
                </button>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="bg-white shadow rounded-lg">
            <div class="p-6">
                <div class="flex justify-end space-x-3 space-x-reverse">
                    <a href="{{ route('admin.products.index') }}"
                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg">
                        انصراف
                    </a>
                    <button type="submit"
                            wire:loading.attr="disabled"
                            class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white px-6 py-2 rounded-lg flex items-center">
                        <span wire:loading.remove>ایجاد محصول</span>
                        <span wire:loading class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            در حال ایجاد...
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
                    <li>فیلدهای ستاره‌دار (*) الزامی هستند</li>
                    <li>اسلاگ به صورت خودکار از ترجمه انگلیسی نام محصول تولید می‌شود</li>
                    <li>حداقل یک دسته‌بندی باید انتخاب شود</li>
                    <li>تصاویر شاخص در صفحه اصلی محصول نمایش داده می‌شوند</li>
                    <li>تنظیمات SEO برای بهبود رتبه در موتورهای جستجو مهم است</li>
                    <li>ویژگی‌های محصول برای نمایش مشخصات فنی استفاده می‌شود</li>
                </ul>
            </div>
        </div>
    </div>
