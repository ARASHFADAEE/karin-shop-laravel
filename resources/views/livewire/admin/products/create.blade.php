<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-900">افزودن محصول جدید</h2>
            <a href="{{ route('admin.products.index') }}"
                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                بازگشت به لیست
            </a>
        </div>
    </div>

    <form wire:submit="save">
        <!-- Tab Navigation -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 space-x-reverse px-6">
                    <button type="button" wire:click="setActiveTab('basic')"
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'basic' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        اطلاعات اصلی
                    </button>
                    <button type="button" wire:click="setActiveTab('variants')"
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'variants' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        انواع محصول
                    </button>
                    <button type="button" wire:click="setActiveTab('filters')"
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'filters' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        فیلترها
                    </button>
                    <button type="button" wire:click="setActiveTab('images')"
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'images' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        تصاویر
                    </button>
                    <button type="button" wire:click="setActiveTab('seo')"
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'seo' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        سئو
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-6">
                <!-- Basic Information Tab -->
                @if($activeTab === 'basic')
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div class="lg:col-span-2">
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">نام محصول *</label>
                                <input type="text" id="name" wire:model="name"
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
                                    <input type="text" id="slug" wire:model="slug"
                                        class="flex-1 border border-gray-300 rounded-r-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="اسلاگ محصول">
                                    <button type="button" wire:click="generateSlug" wire:loading.attr="disabled"
                                        wire:target="generateSlug"
                                        class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white px-4 py-2 rounded-l-lg border border-blue-600">
                                        <span wire:loading.remove wire:target="generateSlug">تولید اسلاگ</span>
                                        <span wire:loading wire:target="generateSlug">در حال تولید...</span>
                                    </button>
                                </div>
                                @error('slug')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="lg:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">توضیحات</label>
                                <textarea id="description" wire:model="description" rows="4"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="توضیحات محصول را وارد کنید"></textarea>
                                @error('description')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Price -->
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700 mb-2">قیمت (تومان) *</label>
                                <input type="number" id="price" wire:model="price"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="قیمت محصول">
                                @error('price')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Original Price -->
                            <div>
                                <label for="original_price" class="block text-sm font-medium text-gray-700 mb-2">قیمت اصلی (تومان)</label>
                                <input type="number" id="original_price" wire:model="original_price"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="قیمت اصلی محصول">
                                @error('original_price')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Stock -->
                            <div>
                                <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">موجودی *</label>
                                <input type="number" id="stock" wire:model="stock"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="تعداد موجودی">
                                @error('stock')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- SKU -->
                            <div>
                                <label for="sku" class="block text-sm font-medium text-gray-700 mb-2">کد محصول (SKU) *</label>
                                <div class="flex">
                                    <input type="text" id="sku" wire:model="sku"
                                        class="flex-1 border border-gray-300 rounded-r-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="کد محصول">
                                    <button type="button" wire:click="generateSku"
                                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-l-lg border border-green-600">
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
                                <select id="status" wire:model="status"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="active">فعال</option>
                                    <option value="draft">پیش‌نویس</option>
                                    <option value="out_of_stock">ناموجود</option>
                                </select>
                                @error('status')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Categories -->
                            <div class="lg:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">دسته‌بندی‌ها *</label>
                                
                                <div wire:listen="categoriesUpdated">
                                    <livewire:category-selector 
                                        :selected-categories="$selectedCategories"
                                        :primary-category="$primaryCategory"
                                        :allow-multiple="true"
                                        :show-hierarchy="true"
                                        :max-height="350"
                                        placeholder="جستجو در دسته‌بندی‌ها..." />
                                </div>
                                
                                @error('selectedCategories')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Variants Tab -->
                @if($activeTab === 'variants')
                    <div class="space-y-6">
                        <!-- Variant Type Selection -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-900 mb-3">نوع محصول</h4>
                            <div class="flex items-center space-x-6 space-x-reverse">
                                <label class="flex items-center">
                                    <input type="radio" wire:model="hasVariants" value="false" class="text-blue-600 focus:ring-blue-500">
                                    <span class="mr-2">محصول ساده</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" wire:model="hasVariants" value="true" class="text-blue-600 focus:ring-blue-500">
                                    <span class="mr-2">محصول با انواع مختلف</span>
                                </label>
                            </div>
                            <p class="text-sm text-gray-600 mt-2">
                                محصول ساده: یک نوع با یک قیمت و موجودی<br>
                                محصول با انواع: چندین نوع با رنگ، سایز و قیمت‌های مختلف
                            </p>
                        </div>

                        @if($hasVariants)
                            <!-- Variant Generation -->
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <h4 class="font-medium text-gray-900 mb-3">تولید خودکار انواع</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Colors -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">رنگ‌ها</label>
                                        <div class="space-y-2 max-h-32 overflow-y-auto">
                                            @foreach(['سفید', 'مشکی', 'قرمز', 'آبی', 'سبز', 'زرد', 'نارنجی', 'بنفش', 'صورتی', 'خاکستری'] as $color)
                                                <label class="flex items-center">
                                                    <input type="checkbox" wire:model="selectedColors" value="{{ $color }}" class="text-blue-600 focus:ring-blue-500">
                                                    <span class="mr-2 text-sm">{{ $color }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Sizes -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">سایزها</label>
                                        <div class="space-y-2 max-h-32 overflow-y-auto">
                                            @foreach(['XS', 'S', 'M', 'L', 'XL', 'XXL', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45'] as $size)
                                                <label class="flex items-center">
                                                    <input type="checkbox" wire:model="selectedSizes" value="{{ $size }}" class="text-blue-600 focus:ring-blue-500">
                                                    <span class="mr-2 text-sm">{{ $size }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <button type="button" wire:click="generateVariantsFromCombinations" 
                                    class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                                    تولید انواع از ترکیب رنگ و سایز
                                </button>
                            </div>

                            <!-- Variants List -->
                            <div>
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="font-medium text-gray-900">لیست انواع محصول</h4>
                                    <button type="button" wire:click="addVariant" 
                                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                                        افزودن نوع جدید
                                    </button>
                                </div>

                                @if(!empty($productVariants))
                                    <div class="space-y-4">
                                        @foreach($productVariants as $index => $variant)
                                            <div class="border border-gray-200 rounded-lg p-4">
                                                <div class="flex justify-between items-start mb-4">
                                                    <h5 class="font-medium text-gray-900">نوع {{ $index + 1 }}</h5>
                                                    <button type="button" wire:click="removeVariant({{ $index }})" 
                                                        class="text-red-600 hover:text-red-800">
                                                        حذف
                                                    </button>
                                                </div>
                                                
                                                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">نام</label>
                                                        <input type="text" wire:model="productVariants.{{ $index }}.name" 
                                                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm" 
                                                            placeholder="نام نوع">
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">رنگ</label>
                                                        <input type="text" wire:model="productVariants.{{ $index }}.color" 
                                                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm" 
                                                            placeholder="رنگ">
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">سایز</label>
                                                        <input type="text" wire:model="productVariants.{{ $index }}.size" 
                                                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm" 
                                                            placeholder="سایز">
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">قیمت</label>
                                                        <input type="number" wire:model="productVariants.{{ $index }}.price" 
                                                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm" 
                                                            placeholder="قیمت">
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">موجودی</label>
                                                        <input type="number" wire:model="productVariants.{{ $index }}.stock" 
                                                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm" 
                                                            placeholder="موجودی">
                                                    </div>
                                                </div>
                                                
                                                <div class="mt-3 text-sm text-gray-600">
                                                    <strong>SKU:</strong> {{ $variant['sku'] ?? 'خودکار تولید می‌شود' }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-8 text-gray-500">
                                        هنوز هیچ نوعی اضافه نشده است
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Filters Tab -->
                @if($activeTab === 'filters')
                    <div class="space-y-6">
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-900 mb-2">فیلترهای محصول</h4>
                            <p class="text-sm text-gray-600">
                                این بخش برای تنظیم فیلترهای جستجو و دسته‌بندی محصول استفاده می‌شود.
                            </p>
                        </div>

                        <!-- Brand -->
                        <div>
                            <label for="selectedBrand" class="block text-sm font-medium text-gray-700 mb-2">برند</label>
                            <input type="text" id="selectedBrand" wire:model="selectedBrand"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="نام برند محصول">
                        </div>

                        <!-- Materials -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">مواد اولیه</label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                @foreach(['پنبه', 'پشم', 'ابریشم', 'پلی‌استر', 'نایلون', 'چرم', 'فلز', 'پلاستیک'] as $material)
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="selectedMaterials" value="{{ $material }}" class="text-blue-600 focus:ring-blue-500">
                                        <span class="mr-2 text-sm">{{ $material }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Product Attributes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">ویژگی‌های اضافی</label>
                            <div class="space-y-3">
                                @foreach($productAttributes as $index => $attribute)
                                    <div class="flex space-x-2 space-x-reverse">
                                        <input type="text" wire:model="productAttributes.{{ $index }}.name" 
                                            class="flex-1 border border-gray-300 rounded-lg px-3 py-2" 
                                            placeholder="نام ویژگی">
                                        <input type="text" wire:model="productAttributes.{{ $index }}.value" 
                                            class="flex-1 border border-gray-300 rounded-lg px-3 py-2" 
                                            placeholder="مقدار ویژگی">
                                        <button type="button" wire:click="removeAttribute({{ $index }})" 
                                            class="text-red-600 hover:text-red-800 px-3">
                                            حذف
                                        </button>
                                    </div>
                                @endforeach
                                <button type="button" wire:click="addAttribute" 
                                    class="text-blue-600 hover:text-blue-800 text-sm">
                                    + افزودن ویژگی
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Images Tab -->
                @if($activeTab === 'images')
                    <div class="space-y-6">
                        <!-- Product Images -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">تصاویر محصول</label>
                            <input type="file" wire:model="images" multiple accept="image/*"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            @error('images.*')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">می‌توانید چندین تصویر انتخاب کنید</p>
                        </div>

                        <!-- Featured Images -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">تصاویر شاخص</label>
                            <input type="file" wire:model="featuredImages" multiple accept="image/*"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            @error('featuredImages.*')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">تصاویری که در صفحه اصلی و لیست محصولات نمایش داده می‌شوند</p>
                        </div>

                        <!-- Image Preview -->
                        @if($images)
                            <div>
                                <h4 class="font-medium text-gray-900 mb-3">پیش‌نمایش تصاویر محصول</h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    @foreach($images as $image)
                                        <div class="border border-gray-200 rounded-lg p-2">
                                            <img src="{{ $image->temporaryUrl() }}" class="w-full h-32 object-cover rounded">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($featuredImages)
                            <div>
                                <h4 class="font-medium text-gray-900 mb-3">پیش‌نمایش تصاویر شاخص</h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    @foreach($featuredImages as $image)
                                        <div class="border border-gray-200 rounded-lg p-2">
                                            <img src="{{ $image->temporaryUrl() }}" class="w-full h-32 object-cover rounded">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- SEO Tab -->
                @if($activeTab === 'seo')
                    <div class="space-y-6">
                        <div class="bg-green-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-900 mb-2">بهینه‌سازی موتورهای جستجو</h4>
                            <p class="text-sm text-gray-600">
                                این اطلاعات برای بهبود رتبه محصول در موتورهای جستجو استفاده می‌شود.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 gap-6">
                            <!-- Meta Title -->
                            <div>
                                <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">عنوان متا</label>
                                <input type="text" id="meta_title" wire:model="meta_title"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="عنوان برای موتورهای جستجو">
                                @error('meta_title')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Meta Description -->
                            <div>
                                <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">توضیحات متا</label>
                                <textarea id="meta_description" wire:model="meta_description" rows="3"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="توضیحات برای موتورهای جستجو"></textarea>
                                @error('meta_description')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Meta Keywords -->
                            <div>
                                <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-2">کلمات کلیدی</label>
                                <input type="text" id="meta_keywords" wire:model="meta_keywords"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="کلمات کلیدی با کاما جدا شوند">
                                @error('meta_keywords')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- OG Title -->
                            <div>
                                <label for="og_title" class="block text-sm font-medium text-gray-700 mb-2">عنوان شبکه‌های اجتماعی</label>
                                <input type="text" id="og_title" wire:model="og_title"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="عنوان برای اشتراک‌گذاری در شبکه‌های اجتماعی">
                                @error('og_title')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- OG Description -->
                            <div>
                                <label for="og_description" class="block text-sm font-medium text-gray-700 mb-2">توضیحات شبکه‌های اجتماعی</label>
                                <textarea id="og_description" wire:model="og_description" rows="3"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="توضیحات برای اشتراک‌گذاری در شبکه‌های اجتماعی"></textarea>
                                @error('og_description')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-3 space-x-reverse">
            <a href="{{ route('admin.products.index') }}"
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg">
                انصراف
            </a>
            <button type="submit" wire:loading.attr="disabled"
                class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white px-6 py-2 rounded-lg">
                <span wire:loading.remove>ذخیره محصول</span>
                <span wire:loading>در حال ذخیره...</span>
            </button>
        </div>
    </form>
</div>
