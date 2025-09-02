<div>
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800 text-center">ورود به پنل مدیریت</h2>
        <p class="text-sm text-gray-600 text-center mt-2">لطفاً اطلاعات خود را وارد کنید</p>
    </div>

    <form wire:submit="login">
        <!-- Email -->
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">ایمیل</label>
            <input type="email" 
                   id="email"
                   wire:model="email" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                   placeholder="admin@example.com"
                   autocomplete="email">
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">رمز عبور</label>
            <input type="password" 
                   id="password"
                   wire:model="password" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                   placeholder="رمز عبور خود را وارد کنید"
                   autocomplete="current-password">
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="mb-6">
            <label class="flex items-center">
                <input type="checkbox" 
                       wire:model="remember" 
                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                <span class="mr-2 text-sm text-gray-600">مرا به خاطر بسپار</span>
            </label>
        </div>

        <!-- Submit Button -->
        <div class="mb-4">
            <button type="submit" 
                    wire:loading.attr="disabled"
                    class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                <span wire:loading.remove>ورود</span>
                <span wire:loading class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    در حال ورود...
                </span>
            </button>
        </div>
    </form>

    <!-- Security Notice -->
    <div class="mt-6 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-yellow-600 mt-0.5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <div>
                <p class="text-sm text-yellow-800 font-medium">نکته امنیتی</p>
                <p class="text-xs text-yellow-700 mt-1">پس از 5 تلاش ناموفق، دسترسی شما به مدت 1 دقیقه محدود خواهد شد.</p>
            </div>
        </div>
    </div>
</div>
