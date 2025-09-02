<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-900">مدیریت تصاویر محصول: {{ $product->name }}</h2>
            <div class="flex space-x-2 space-x-reverse">
                <a href="{{ route('admin.products.show', $product) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    مشاهده محصول
                </a>
                <a href="{{ route('admin.products.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                    بازگشت به لیست
                </a>
            </div>
        </div>
    </div>

    <!-- Upload Section -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">آپلود تصاویر جدید</h3>
        
        <form wire:submit="uploadImages">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Regular Images Upload -->
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

                <!-- Featured Images Upload -->
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

            <!-- Upload Button -->
            @if($images || $featuredImages)
                <div class="mt-6 flex justify-center">
                    <button type="submit" 
                            wire:loading.attr="disabled"
                            class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white px-6 py-3 rounded-lg flex items-center">
                        <span wire:loading.remove wire:target="uploadImages">آپلود تصاویر</span>
                        <span wire:loading wire:target="uploadImages" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            در حال آپلود...
                        </span>
                    </button>
                </div>
            @endif
        </form>
    </div>

    <!-- Current Images -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Featured Images -->
        @if($product->featuredImages->count() > 0)
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 text-yellow-500 ml-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                    تصاویر شاخص ({{ $product->featuredImages->count() }})
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($product->featuredImages as $image)
                        <div class="relative group">
                            <img src="{{ $image->image_path }}" 
                                 alt="{{ $image->alt_text }}" 
                                 class="w-full h-32 object-cover rounded-lg border-2 border-yellow-200">
                            <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                                <button wire:click="deleteImage({{ $image->id }}, 'featured')" 
                                        wire:confirm="آیا از حذف این تصویر اطمینان دارید؟"
                                        class="bg-red-600 hover:bg-red-700 text-white p-2 rounded-full">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                            <span class="absolute top-2 right-2 bg-yellow-500 text-white text-xs px-2 py-1 rounded">شاخص</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Regular Images -->
        @if($product->images->count() > 0)
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">تصاویر عادی ({{ $product->images->count() }})</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($product->images as $image)
                        <div class="relative group">
                            <img src="{{ $image->image_path }}" 
                                 alt="{{ $image->alt_text }}" 
                                 class="w-full h-32 object-cover rounded-lg">
                            <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center space-x-2 space-x-reverse">
                                <button wire:click="setAsFeatured({{ $image->id }})" 
                                        class="bg-yellow-600 hover:bg-yellow-700 text-white p-2 rounded-full" 
                                        title="تنظیم به عنوان تصویر شاخص">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                </button>
                                <button wire:click="deleteImage({{ $image->id }})" 
                                        wire:confirm="آیا از حذف این تصویر اطمینان دارید؟"
                                        class="bg-red-600 hover:bg-red-700 text-white p-2 rounded-full">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Empty State -->
    @if($product->images->count() === 0 && $product->featuredImages->count() === 0)
        <div class="bg-white shadow rounded-lg p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">هنوز تصویری آپلود نشده</h3>
            <p class="mt-2 text-sm text-gray-500">برای شروع، تصاویر محصول را از بخش بالا آپلود کنید.</p>
        </div>
    @endif

    <!-- Info Box -->
    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-blue-600 mt-0.5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <p class="text-sm text-blue-800 font-medium">نکات مهم</p>
                <ul class="text-xs text-blue-700 mt-1 list-disc list-inside">
                    <li>حداکثر حجم هر تصویر 2 مگابایت است</li>
                    <li>فرمت‌های مجاز: JPG, PNG, GIF</li>
                    <li>تصاویر شاخص در صفحه اصلی محصول نمایش داده می‌شوند</li>
                    <li>می‌توانید تصاویر عادی را به تصویر شاخص تبدیل کنید</li>
                    <li>تصاویر در پوشه storage/app/public/products ذخیره می‌شوند</li>
                </ul>
            </div>
        </div>
    </div>
</div>
