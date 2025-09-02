<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-900">جزئیات محصول: {{ $product->name }}</h2>
            <div class="flex space-x-2 space-x-reverse">
                <a href="{{ route('admin.products.edit', $product) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    ویرایش محصول
                </a>
                <a href="{{ route('admin.products.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                    بازگشت به لیست
                </a>
            </div>
        </div>
    </div>

    <!-- Product Info Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">اطلاعات اصلی</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">نام محصول</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $product->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">کد محصول (SKU)</label>
                    <p class="mt-1 text-sm text-gray-900 font-mono">{{ $product->sku }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">دسته‌بندی</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $product->category->name ?? 'ندارد' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Slug</label>
                    <p class="mt-1 text-sm text-gray-900 font-mono">{{ $product->slug }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">قیمت</label>
                    <p class="mt-1 text-sm text-gray-900 font-bold">{{ number_format($product->price) }} تومان</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">موجودی</label>
                    <div class="flex items-center space-x-2 space-x-reverse">
                        <span class="text-sm {{ $product->stock <= 5 ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                            {{ $product->stock }} عدد
                        </span>
                        @if($product->stock <= 5)
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                موجودی کم
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            
            @if($product->description)
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">توضیحات</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $product->description }}</p>
                </div>
            @endif
        </div>

        <!-- Status & Actions -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">وضعیت و عملیات</h3>
            
            <!-- Current Status -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">وضعیت فعلی</label>
                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                    @if($product->status === 'active') bg-green-100 text-green-800
                    @elseif($product->status === 'draft') bg-yellow-100 text-yellow-800
                    @else bg-red-100 text-red-800
                    @endif">
                    @switch($product->status)
                        @case('active') فعال @break
                        @case('draft') پیش‌نویس @break
                        @case('out_of_stock') ناموجود @break
                        @default {{ $product->status }}
                    @endswitch
                </span>
            </div>

            <!-- Toggle Status -->
            <div class="mb-4">
                <button wire:click="toggleStatus" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                    تغییر وضعیت
                </button>
            </div>

            <!-- Quick Stock Update -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">به‌روزرسانی سریع موجودی</label>
                <div class="flex space-x-2 space-x-reverse">
                    <button wire:click="updateStock({{ $product->stock + 10 }})" 
                            class="flex-1 bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded text-xs">
                        +10
                    </button>
                    <button wire:click="updateStock({{ max(0, $product->stock - 10) }})" 
                            class="flex-1 bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded text-xs">
                        -10
                    </button>
                </div>
            </div>

            <!-- Timestamps -->
            <div class="text-xs text-gray-500 space-y-1">
                <div>ایجاد: {{ $product->created_at->format('Y/m/d H:i') }}</div>
                <div>به‌روزرسانی: {{ $product->updated_at->format('Y/m/d H:i') }}</div>
            </div>
        </div>
    </div>

    <!-- Images Section -->
    @if($product->images->count() > 0 || $product->featuredImages->count() > 0)
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">تصاویر محصول</h3>
            
            @if($product->featuredImages->count() > 0)
                <div class="mb-4">
                    <h4 class="text-md font-medium text-gray-700 mb-2">تصاویر شاخص</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($product->featuredImages as $image)
                            <div class="relative">
                                <img src="{{ $image->image_url }}" alt="Featured Image" class="w-full h-32 object-cover rounded-lg">
                                <span class="absolute top-2 right-2 bg-yellow-500 text-white text-xs px-2 py-1 rounded">شاخص</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($product->images->count() > 0)
                <div>
                    <h4 class="text-md font-medium text-gray-700 mb-2">تصاویر عادی</h4>
                    <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                        @foreach($product->images as $image)
                            <img src="{{ $image->image_url }}" alt="Product Image" class="w-full h-24 object-cover rounded-lg">
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Attributes Section -->
    @if($product->attributes->count() > 0)
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">ویژگی‌های محصول</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($product->attributes as $attribute)
                    <div class="border border-gray-200 rounded-lg p-3">
                        <div class="font-medium text-gray-900">{{ $attribute->attribute_name }}</div>
                        <div class="text-sm text-gray-600">{{ $attribute->attribute_value }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Reviews Section -->
    @if($product->reviews->count() > 0)
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">نظرات کاربران ({{ $product->reviews->count() }})</h3>
            <div class="space-y-4">
                @foreach($product->reviews->take(5) as $review)
                    <div class="border-b border-gray-200 pb-4 last:border-b-0">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="font-medium text-gray-900">{{ $review->user->name }}</div>
                                <div class="flex items-center mt-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    @endfor
                                    <span class="mr-2 text-sm text-gray-600">{{ $review->rating }}/5</span>
                                </div>
                            </div>
                            <div class="text-sm text-gray-500">{{ $review->created_at->format('Y/m/d') }}</div>
                        </div>
                        @if($review->comment)
                            <p class="mt-2 text-sm text-gray-700">{{ $review->comment }}</p>
                        @endif
                    </div>
                @endforeach
                
                @if($product->reviews->count() > 5)
                    <div class="text-center">
                        <span class="text-sm text-gray-500">و {{ $product->reviews->count() - 5 }} نظر دیگر...</span>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
