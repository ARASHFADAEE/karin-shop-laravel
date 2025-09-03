<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-900">تنظیمات سیستم</h2>
            <button wire:click="clearCache" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm">
                پاک کردن کش
            </button>
        </div>
    </div>

    <form wire:submit="save">
        <!-- General Settings -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">تنظیمات عمومی</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="site_name" class="block text-sm font-medium text-gray-700 mb-2">نام سایت *</label>
                    <input type="text" 
                           id="site_name"
                           wire:model="site_name" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="فروشگاه کارین">
                    @error('site_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">ایمیل تماس</label>
                    <input type="email" 
                           id="contact_email"
                           wire:model="contact_email" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="info@karinshop.com">
                    @error('contact_email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">تلفن تماس</label>
                    <input type="text" 
                           id="contact_phone"
                           wire:model="contact_phone" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="021-12345678">
                    @error('contact_phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="site_description" class="block text-sm font-medium text-gray-700 mb-2">توضیحات سایت</label>
                    <textarea wire:model="site_description" 
                              id="site_description"
                              rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="توضیحات کوتاه درباره فروشگاه..."></textarea>
                    @error('site_description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="site_address" class="block text-sm font-medium text-gray-700 mb-2">آدرس فروشگاه</label>
                    <textarea wire:model="site_address" 
                              id="site_address"
                              rows="2"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="آدرس کامل فروشگاه..."></textarea>
                    @error('site_address')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Shop Settings -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">تنظیمات فروشگاه</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">واحد پول *</label>
                    <input type="text" 
                           id="currency"
                           wire:model="currency" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="تومان">
                    @error('currency')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="shipping_cost" class="block text-sm font-medium text-gray-700 mb-2">هزینه ارسال *</label>
                    <input type="number" 
                           id="shipping_cost"
                           wire:model="shipping_cost" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="0"
                           min="0">
                    @error('shipping_cost')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="free_shipping_threshold" class="block text-sm font-medium text-gray-700 mb-2">حد ارسال رایگان *</label>
                    <input type="number" 
                           id="free_shipping_threshold"
                           wire:model="free_shipping_threshold" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="500000"
                           min="0">
                    @error('free_shipping_threshold')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="max_order_items" class="block text-sm font-medium text-gray-700 mb-2">حداکثر آیتم سفارش *</label>
                    <input type="number" 
                           id="max_order_items"
                           wire:model="max_order_items" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="10"
                           min="1">
                    @error('max_order_items')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Payment Settings -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">تنظیمات درگاه پرداخت</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="zarinpal_merchant_id" class="block text-sm font-medium text-gray-700 mb-2">Merchant ID زرین‌پال</label>
                    <input type="text" 
                           id="zarinpal_merchant_id"
                           wire:model="zarinpal_merchant_id" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx">
                    @error('zarinpal_merchant_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="mellat_terminal_id" class="block text-sm font-medium text-gray-700 mb-2">Terminal ID بانک ملت</label>
                    <input type="text" 
                           id="mellat_terminal_id"
                           wire:model="mellat_terminal_id" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="123456">
                    @error('mellat_terminal_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="mellat_username" class="block text-sm font-medium text-gray-700 mb-2">نام کاربری بانک ملت</label>
                    <input type="text" 
                           id="mellat_username"
                           wire:model="mellat_username" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="username">
                    @error('mellat_username')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="mellat_password" class="block text-sm font-medium text-gray-700 mb-2">رمز عبور بانک ملت</label>
                    <input type="password" 
                           id="mellat_password"
                           wire:model="mellat_password" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="••••••••">
                    @error('mellat_password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- SMS Settings -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">تنظیمات پیامک</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="sms_api_key" class="block text-sm font-medium text-gray-700 mb-2">API Key پیامک</label>
                    <input type="text" 
                           id="sms_api_key"
                           wire:model="sms_api_key" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="your-sms-api-key">
                    @error('sms_api_key')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="sms_sender_number" class="block text-sm font-medium text-gray-700 mb-2">شماره فرستنده</label>
                    <input type="text" 
                           id="sms_sender_number"
                           wire:model="sms_sender_number" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="10008888">
                    @error('sms_sender_number')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Email Settings -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">تنظیمات ایمیل (SMTP)</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="smtp_host" class="block text-sm font-medium text-gray-700 mb-2">SMTP Host</label>
                    <input type="text" 
                           id="smtp_host"
                           wire:model="smtp_host" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="smtp.gmail.com">
                    @error('smtp_host')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="smtp_port" class="block text-sm font-medium text-gray-700 mb-2">SMTP Port</label>
                    <input type="number" 
                           id="smtp_port"
                           wire:model="smtp_port" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="587"
                           min="1"
                           max="65535">
                    @error('smtp_port')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="smtp_username" class="block text-sm font-medium text-gray-700 mb-2">SMTP Username</label>
                    <input type="text" 
                           id="smtp_username"
                           wire:model="smtp_username" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="your-email@gmail.com">
                    @error('smtp_username')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="smtp_password" class="block text-sm font-medium text-gray-700 mb-2">SMTP Password</label>
                    <input type="password" 
                           id="smtp_password"
                           wire:model="smtp_password" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="••••••••••••••••">
                    @error('smtp_password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Invoice Settings -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">تنظیمات فاکتور</h3>
            
            <!-- Company Information -->
            <div class="mb-6">
                <h4 class="text-md font-medium text-gray-800 mb-4">اطلاعات شرکت</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="invoice_company_name" class="block text-sm font-medium text-gray-700 mb-2">نام شرکت</label>
                        <input type="text" 
                               id="invoice_company_name"
                               wire:model="invoice_company_name" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="کارین شاپ">
                        @error('invoice_company_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="invoice_company_phone" class="block text-sm font-medium text-gray-700 mb-2">تلفن شرکت</label>
                        <input type="text" 
                               id="invoice_company_phone"
                               wire:model="invoice_company_phone" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="۰۹۱۴۰۰۶۳۷۹">
                        @error('invoice_company_phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="invoice_company_email" class="block text-sm font-medium text-gray-700 mb-2">ایمیل شرکت</label>
                        <input type="email" 
                               id="invoice_company_email"
                               wire:model="invoice_company_email" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="info@karinshop.com">
                        @error('invoice_company_email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="invoice_company_website" class="block text-sm font-medium text-gray-700 mb-2">وب‌سایت شرکت</label>
                        <input type="url" 
                               id="invoice_company_website"
                               wire:model="invoice_company_website" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="https://karinshop.com">
                        @error('invoice_company_website')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-4">
                    <label for="invoice_company_address" class="block text-sm font-medium text-gray-700 mb-2">آدرس شرکت</label>
                    <textarea id="invoice_company_address"
                              wire:model="invoice_company_address" 
                              rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="آدرس کامل شرکت..."></textarea>
                    @error('invoice_company_address')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Design Settings -->
            <div class="mb-6">
                <h4 class="text-md font-medium text-gray-800 mb-4">تنظیمات طراحی</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="invoice_logo_url" class="block text-sm font-medium text-gray-700 mb-2">لینک لوگو</label>
                        <input type="url" 
                               id="invoice_logo_url"
                               wire:model="invoice_logo_url" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="https://example.com/logo.png">
                        @error('invoice_logo_url')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="invoice_primary_color" class="block text-sm font-medium text-gray-700 mb-2">رنگ اصلی</label>
                        <input type="color" 
                               id="invoice_primary_color"
                               wire:model="invoice_primary_color" 
                               class="w-full h-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('invoice_primary_color')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="invoice_secondary_color" class="block text-sm font-medium text-gray-700 mb-2">رنگ فرعی</label>
                        <input type="color" 
                               id="invoice_secondary_color"
                               wire:model="invoice_secondary_color" 
                               class="w-full h-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('invoice_secondary_color')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Footer Settings -->
            <div class="mb-6">
                <h4 class="text-md font-medium text-gray-800 mb-4">تنظیمات فوتر و واترمارک</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="invoice_footer_text" class="block text-sm font-medium text-gray-700 mb-2">متن فوتر</label>
                        <input type="text" 
                               id="invoice_footer_text"
                               wire:model="invoice_footer_text" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="با تشکر از خرید شما">
                        @error('invoice_footer_text')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="invoice_watermark_text" class="block text-sm font-medium text-gray-700 mb-2">متن واترمارک</label>
                        <input type="text" 
                               id="invoice_watermark_text"
                               wire:model="invoice_watermark_text" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="کارین شاپ">
                        @error('invoice_watermark_text')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-4">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               wire:model="invoice_show_watermark" 
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="mr-2 text-sm text-gray-700">نمایش واترمارک در فاکتور</span>
                    </label>
                </div>
                
                <div class="mt-4">
                    <label for="invoice_terms" class="block text-sm font-medium text-gray-700 mb-2">شرایط و قوانین</label>
                    <textarea id="invoice_terms"
                              wire:model="invoice_terms" 
                              rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="شرایط و قوانین فروش..."></textarea>
                    @error('invoice_terms')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Melli Payamak SMS Settings -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">تنظیمات ملی پیامک</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="melli_payamak_username" class="block text-sm font-medium text-gray-700 mb-2">نام کاربری</label>
                    <input type="text" 
                           id="melli_payamak_username"
                           wire:model="melli_payamak_username" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="نام کاربری ملی پیامک">
                    @error('melli_payamak_username')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="melli_payamak_password" class="block text-sm font-medium text-gray-700 mb-2">رمز عبور</label>
                    <input type="password" 
                           id="melli_payamak_password"
                           wire:model="melli_payamak_password" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="رمز عبور ملی پیامک">
                    @error('melli_payamak_password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="melli_payamak_sender_number" class="block text-sm font-medium text-gray-700 mb-2">شماره فرستنده</label>
                    <input type="text" 
                           id="melli_payamak_sender_number"
                           wire:model="melli_payamak_sender_number" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="شماره فرستنده">
                    @error('melli_payamak_sender_number')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- SMS Pattern Settings -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">تنظیمات پترن پیامکی</h3>
            
            <div class="mb-6">
                <h4 class="text-md font-medium text-gray-800 mb-4">پترن‌های مشتری</h4>
                <div class="space-y-4">
                    <div>
                        <label for="sms_pattern_login_code" class="block text-sm font-medium text-gray-700 mb-2">کد ورود یکبار مصرف</label>
                        <input type="text" 
                               id="sms_pattern_login_code"
                               wire:model="sms_pattern_login_code" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="کد پترن برای کد ورود">
                        <p class="text-xs text-gray-500 mt-1">نمونه پیامک: کد یک بار مصرف شما {0} میباشد</p>
                        @error('sms_pattern_login_code')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sms_pattern_order_created" class="block text-sm font-medium text-gray-700 mb-2">ثبت سفارش</label>
                        <input type="text" 
                               id="sms_pattern_order_created"
                               wire:model="sms_pattern_order_created" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="کد پترن برای ثبت سفارش">
                        <p class="text-xs text-gray-500 mt-1">نمونه پیامک: سلام {0} عزیز، سفارش {1} دریافت شد و هم اکنون در وضعیت در انتظار پرداخت می‌باشد. آیتم های سفارش: {2} مبلغ سفارش: {3}</p>
                        @error('sms_pattern_order_created')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sms_pattern_order_processing" class="block text-sm font-medium text-gray-700 mb-2">در حال پردازش</label>
                        <input type="text" 
                               id="sms_pattern_order_processing"
                               wire:model="sms_pattern_order_processing" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="کد پترن برای وضعیت در حال پردازش">
                        <p class="text-xs text-gray-500 mt-1">نمونه پیامک: سلام {0} عزیز، وضعیت سفارش {1} به {2} تغییر یافت.</p>
                        @error('sms_pattern_order_processing')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sms_pattern_order_shipped" class="block text-sm font-medium text-gray-700 mb-2">ارسال شده</label>
                        <input type="text" 
                               id="sms_pattern_order_shipped"
                               wire:model="sms_pattern_order_shipped" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="کد پترن برای وضعیت ارسال شده">
                        <p class="text-xs text-gray-500 mt-1">نمونه پیامک: سلام {0} عزیز، وضعیت سفارش {1} به {2} تغییر یافت.</p>
                        @error('sms_pattern_order_shipped')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sms_pattern_order_delivered" class="block text-sm font-medium text-gray-700 mb-2">تحویل داده شده</label>
                        <input type="text" 
                               id="sms_pattern_order_delivered"
                               wire:model="sms_pattern_order_delivered" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="کد پترن برای وضعیت تحویل داده شده">
                        <p class="text-xs text-gray-500 mt-1">نمونه پیامک: سلام {0} عزیز، وضعیت سفارش {1} به {2} تغییر یافت.</p>
                        @error('sms_pattern_order_delivered')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sms_pattern_order_cancelled" class="block text-sm font-medium text-gray-700 mb-2">لغو شده</label>
                        <input type="text" 
                               id="sms_pattern_order_cancelled"
                               wire:model="sms_pattern_order_cancelled" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="کد پترن برای وضعیت لغو شده">
                        <p class="text-xs text-gray-500 mt-1">نمونه پیامک: سلام {0} عزیز، وضعیت سفارش {1} به {2} تغییر یافت.</p>
                        @error('sms_pattern_order_cancelled')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="mb-6">
                <h4 class="text-md font-medium text-gray-800 mb-4">پترن‌های مدیریتی</h4>
                <div class="space-y-4">
                    <div>
                        <label for="sms_pattern_admin_new_order" class="block text-sm font-medium text-gray-700 mb-2">سفارش جدید برای ادمین</label>
                        <input type="text" 
                               id="sms_pattern_admin_new_order"
                               wire:model="sms_pattern_admin_new_order" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="کد پترن برای اطلاع ادمین از سفارش جدید">
                        <p class="text-xs text-gray-500 mt-1">نمونه پیامک: سفارش جدید - شماره: {0} مشتری: {1} مبلغ: {2}</p>
                        @error('sms_pattern_admin_new_order')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sms_pattern_admin_low_stock" class="block text-sm font-medium text-gray-700 mb-2">کمبود موجودی</label>
                        <input type="text" 
                               id="sms_pattern_admin_low_stock"
                               wire:model="sms_pattern_admin_low_stock" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="کد پترن برای اطلاع ادمین از کمبود موجودی">
                        <p class="text-xs text-gray-500 mt-1">نمونه پیامک: هشدار کمبود موجودی - محصول: {0} موجودی باقیمانده: {1}</p>
                        @error('sms_pattern_admin_low_stock')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h5 class="text-sm font-medium text-blue-800 mb-2">راهنمای استفاده از پترن‌ها</h5>
                <ul class="text-xs text-blue-700 space-y-1">
                    <li>• {0}, {1}, {2} و ... نشان‌دهنده متغیرهای پیامک هستند</li>
                    <li>• این پترن‌ها باید در پنل ملی پیامک شما تعریف شده باشند</li>
                    <li>• در صورت خالی بودن پترن، پیامک پیش‌فرض ارسال می‌شود</li>
                    <li>• برای تست پترن‌ها، ابتدا آن‌ها را در پنل ملی پیامک تست کنید</li>
                </ul>
            </div>
        </div>

        <!-- Security Warning -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-yellow-600 mt-0.5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <div>
                    <p class="text-sm text-yellow-800 font-medium">نکات امنیتی</p>
                    <ul class="text-xs text-yellow-700 mt-1 list-disc list-inside">
                        <li>اطلاعات حساس مانند رمز عبور درگاه‌ها را محرمانه نگه دارید</li>
                        <li>تنظیمات در کش ذخیره می‌شوند و برای 30 روز معتبر هستند</li>
                        <li>پس از تغییر تنظیمات، سیستم را مجدداً تست کنید</li>
                        <li>از API Key های معتبر و فعال استفاده کنید</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">
            <button type="submit" 
                    wire:loading.attr="disabled"
                    class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white px-6 py-3 rounded-lg flex items-center">
                <span wire:loading.remove>ذخیره تنظیمات</span>
                <span wire:loading class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    در حال ذخیره...
                </span>
            </button>
        </div>
    </form>
</div>
