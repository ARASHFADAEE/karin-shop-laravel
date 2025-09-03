<div>
    @section('title', 'گالری رسانه')
    
    <div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">گالری رسانه</h1>
            <p class="text-gray-600 mt-1">مدیریت فایل‌ها و تصاویر</p>
        </div>
        <button wire:click="openUploadModal" 
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            آپلود فایل
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="mr-3">
                    <p class="text-sm text-gray-600">کل فایل‌ها</p>
                    <p class="text-lg font-semibold">{{ number_format($stats['total_files']) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="mr-3">
                    <p class="text-sm text-gray-600">تصاویر</p>
                    <p class="text-lg font-semibold">{{ number_format($stats['images_count']) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="mr-3">
                    <p class="text-sm text-gray-600">ویدیوها</p>
                    <p class="text-lg font-semibold">{{ number_format($stats['videos_count']) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-orange-100 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="mr-3">
                    <p class="text-sm text-gray-600">اسناد</p>
                    <p class="text-lg font-semibold">{{ number_format($stats['documents_count']) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-gray-100 rounded-lg">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                    </svg>
                </div>
                <div class="mr-3">
                    <p class="text-sm text-gray-600">حجم کل</p>
                    <p class="text-lg font-semibold">{{ number_format($stats['total_size'] / 1024 / 1024, 1) }} MB</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <!-- Search -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">جستجو</label>
                <input type="text" wire:model.live="search" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="نام فایل، توضیحات یا تگ...">
            </div>
            
            <!-- Type Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">نوع فایل</label>
                <select wire:model.live="filterType" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach($fileTypes as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Folder Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">پوشه</label>
                <select wire:model.live="filterFolder" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">همه پوشه‌ها</option>
                    @foreach($folders as $folder)
                        <option value="{{ $folder }}">{{ $folder }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Date From -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">از تاریخ</label>
                <input type="date" wire:model.live="dateFrom" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <!-- Date To -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">تا تاریخ</label>
                <input type="date" wire:model.live="dateTo" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <!-- Clear Filters -->
            <div class="flex items-end">
                <button wire:click="clearFilters" 
                        class="w-full bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded-lg transition-colors">
                    پاک کردن فیلترها
                </button>
            </div>
        </div>
    </div>

    <!-- Toolbar -->
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center space-x-4 space-x-reverse">
            <!-- View Mode -->
            <div class="flex bg-gray-100 rounded-lg p-1">
                <button wire:click="setViewMode('grid')" 
                        class="px-3 py-1 rounded {{ $viewMode === 'grid' ? 'bg-white shadow' : '' }} transition-all">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                </button>
                <button wire:click="setViewMode('list')" 
                        class="px-3 py-1 rounded {{ $viewMode === 'list' ? 'bg-white shadow' : '' }} transition-all">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Per Page -->
            <select wire:model.live="perPage" 
                    class="border border-gray-300 rounded-lg px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="12">12</option>
                <option value="24">24</option>
                <option value="48">48</option>
                <option value="96">96</option>
            </select>
        </div>
        
        <div class="flex items-center space-x-2 space-x-reverse">
            <!-- Sort Options -->
            <select wire:model.live="sortBy" 
                    class="border border-gray-300 rounded-lg px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="created_at">تاریخ ایجاد</option>
                <option value="name">نام</option>
                <option value="size">حجم</option>
                <option value="download_count">تعداد دانلود</option>
            </select>
            
            <button wire:click="setSortBy('{{ $sortBy }}')" 
                    class="p-1 border border-gray-300 rounded hover:bg-gray-50">
                <svg class="w-4 h-4 {{ $sortDirection === 'desc' ? 'transform rotate-180' : '' }}" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Files Grid/List -->
    @if($viewMode === 'grid')
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
            @forelse($mediaFiles as $file)
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow cursor-pointer group"
                     wire:click="selectFile({{ $file->id }})">
                    <div class="aspect-square bg-gray-100 rounded-t-lg overflow-hidden relative">
                        @if($file->isImage())
                            <img src="{{ $file->getThumbnailUrl('medium') ?: $file->getFullUrl() }}" 
                                 alt="{{ $file->alt_text ?: $file->name }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-4xl">
                                {{ $file->getTypeIcon() }}
                            </div>
                        @endif
                        
                        <!-- File Type Badge -->
                        <div class="absolute top-2 left-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">
                            {{ strtoupper($file->extension) }}
                        </div>
                        
                        <!-- Public Badge -->
                        @if($file->is_public)
                            <div class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded">
                                عمومی
                            </div>
                        @endif
                    </div>
                    
                    <div class="p-3">
                        <h3 class="font-medium text-sm text-gray-900 truncate" title="{{ $file->name }}">{{ $file->name }}</h3>
                        <p class="text-xs text-gray-500 mt-1">{{ $file->human_size }}</p>
                        @if($file->folder)
                            <p class="text-xs text-blue-600 mt-1">📁 {{ $file->folder }}</p>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">فایلی یافت نشد</h3>
                    <p class="mt-1 text-sm text-gray-500">فایل جدید آپلود کنید یا فیلترها را تغییر دهید.</p>
                </div>
            @endforelse
        </div>
    @else
        <!-- List View -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">فایل</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">نوع</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">حجم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">پوشه</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاریخ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">عملیات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($mediaFiles as $file)
                        <tr class="hover:bg-gray-50 cursor-pointer" wire:click="selectFile({{ $file->id }})">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if($file->isImage())
                                            <img class="h-10 w-10 rounded object-cover" 
                                                 src="{{ $file->getThumbnailUrl('small') ?: $file->getFullUrl() }}" 
                                                 alt="{{ $file->alt_text ?: $file->name }}">
                                        @else
                                            <div class="h-10 w-10 bg-gray-100 rounded flex items-center justify-center text-lg">
                                                {{ $file->getTypeIcon() }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mr-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $file->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $file->original_name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ strtoupper($file->extension) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $file->human_size }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $file->folder ?: '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $file->created_at->format('Y/m/d H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button wire:click.stop="copyFileUrl({{ $file->id }})" 
                                        class="text-blue-600 hover:text-blue-900 ml-3">کپی لینک</button>
                                <button wire:click.stop="downloadFile({{ $file->id }})" 
                                        class="text-green-600 hover:text-green-900 ml-3">دانلود</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">فایلی یافت نشد</h3>
                                <p class="mt-1 text-sm text-gray-500">فایل جدید آپلود کنید یا فیلترها را تغییر دهید.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    <!-- Pagination -->
    {{ $mediaFiles->links() }}

    <!-- Upload Modal -->
    @if($showUploadModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeUploadModal">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white" wire:click.stop>
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">آپلود فایل جدید</h3>
                    
                    <form wire:submit="uploadFiles">
                        <!-- File Upload -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">انتخاب فایل‌ها</label>
                            <input type="file" wire:model="files" multiple 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar">
                            @error('files.*') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <!-- Folder -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">پوشه (اختیاری)</label>
                            <input type="text" wire:model="uploadFolder" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="نام پوشه...">
                        </div>
                        
                        <!-- Description -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">توضیحات (اختیاری)</label>
                            <textarea wire:model="uploadDescription" rows="3"
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="توضیحات فایل..."></textarea>
                        </div>
                        
                        <!-- Alt Text -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">متن جایگزین (برای تصاویر)</label>
                            <input type="text" wire:model="uploadAltText" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="متن جایگزین تصویر...">
                        </div>
                        
                        <!-- Tags -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">تگ‌ها (با کاما جدا کنید)</label>
                            <input type="text" wire:model="uploadTags" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="تگ1, تگ2, تگ3...">
                        </div>
                        
                        <!-- Public Checkbox -->
                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="makePublic" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="mr-2 text-sm text-gray-700">فایل عمومی باشد</span>
                            </label>
                        </div>
                        
                        <!-- Buttons -->
                        <div class="flex justify-end space-x-3 space-x-reverse">
                            <button type="button" wire:click="closeUploadModal" 
                                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition-colors">
                                انصراف
                            </button>
                            <button type="submit" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors"
                                    wire:loading.attr="disabled">
                                <span wire:loading.remove>آپلود</span>
                                <span wire:loading>در حال آپلود...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- File Details Modal -->
    @if($showDetailsModal && $selectedFile)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeDetailsModal">
            <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white" wire:click.stop>
                <div class="mt-3">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-lg font-medium text-gray-900">جزئیات فایل</h3>
                        <button wire:click="closeDetailsModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- File Preview -->
                        <div>
                            @if($selectedFile->isImage())
                                <img src="{{ $selectedFile->getFullUrl() }}" 
                                     alt="{{ $selectedFile->alt_text ?: $selectedFile->name }}"
                                     class="w-full rounded-lg shadow">
                            @elseif($selectedFile->isVideo())
                                <video controls class="w-full rounded-lg shadow">
                                    <source src="{{ $selectedFile->getFullUrl() }}" type="{{ $selectedFile->mime_type }}">
                                    مرورگر شما از پخش ویدیو پشتیبانی نمی‌کند.
                                </video>
                            @else
                                <div class="w-full h-64 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <div class="text-center">
                                        <div class="text-6xl mb-4">{{ $selectedFile->getTypeIcon() }}</div>
                                        <p class="text-gray-600">{{ $selectedFile->original_name }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <!-- File Info -->
                        <div>
                            @if($editingFile && $editingFile->id === $selectedFile->id)
                                <!-- Edit Form -->
                                <form wire:submit="updateFile">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">نام</label>
                                            <input type="text" wire:model="editName" 
                                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            @error('editName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">توضیحات</label>
                                            <textarea wire:model="editDescription" rows="3"
                                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">متن جایگزین</label>
                                            <input type="text" wire:model="editAltText" 
                                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">تگ‌ها</label>
                                            <input type="text" wire:model="editTags" 
                                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">پوشه</label>
                                            <input type="text" wire:model="editFolder" 
                                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        
                                        <div class="flex space-x-3 space-x-reverse">
                                            <button type="submit" 
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                                                ذخیره
                                            </button>
                                            <button type="button" wire:click="cancelEdit" 
                                                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition-colors">
                                                انصراف
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            @else
                                <!-- View Mode -->
                                <div class="space-y-4">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-700">نام فایل</h4>
                                        <p class="text-gray-900">{{ $selectedFile->name }}</p>
                                    </div>
                                    
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-700">نام اصلی</h4>
                                        <p class="text-gray-900">{{ $selectedFile->original_name }}</p>
                                    </div>
                                    
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-700">نوع فایل</h4>
                                        <p class="text-gray-900">{{ $selectedFile->mime_type }}</p>
                                    </div>
                                    
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-700">حجم</h4>
                                        <p class="text-gray-900">{{ $selectedFile->human_size }}</p>
                                    </div>
                                    
                                    @if($selectedFile->width && $selectedFile->height)
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-700">ابعاد</h4>
                                            <p class="text-gray-900">{{ $selectedFile->width }} × {{ $selectedFile->height }} پیکسل</p>
                                        </div>
                                    @endif
                                    
                                    @if($selectedFile->folder)
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-700">پوشه</h4>
                                            <p class="text-gray-900">{{ $selectedFile->folder }}</p>
                                        </div>
                                    @endif
                                    
                                    @if($selectedFile->description)
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-700">توضیحات</h4>
                                            <p class="text-gray-900">{{ $selectedFile->description }}</p>
                                        </div>
                                    @endif
                                    
                                    @if($selectedFile->tags)
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-700">تگ‌ها</h4>
                                            <div class="flex flex-wrap gap-2 mt-1">
                                                @foreach($selectedFile->tags as $tag)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ $tag }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-700">تاریخ آپلود</h4>
                                        <p class="text-gray-900">{{ $selectedFile->created_at->format('Y/m/d H:i') }}</p>
                                    </div>
                                    
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-700">تعداد دانلود</h4>
                                        <p class="text-gray-900">{{ number_format($selectedFile->download_count) }}</p>
                                    </div>
                                    
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-700">لینک فایل</h4>
                                        <div class="flex items-center space-x-2 space-x-reverse">
                                            <input type="text" value="{{ $selectedFile->getFullUrl() }}" readonly 
                                                   class="flex-1 border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 text-sm">
                                            <button wire:click="copyFileUrl({{ $selectedFile->id }})" 
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm transition-colors">
                                                کپی
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Action Buttons -->
                                    <div class="flex flex-wrap gap-2 pt-4 border-t">
                                        <button wire:click="editFile({{ $selectedFile->id }})" 
                                                class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-2 rounded-lg text-sm transition-colors">
                                            ویرایش
                                        </button>
                                        
                                        <button wire:click="downloadFile({{ $selectedFile->id }})" 
                                                class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg text-sm transition-colors">
                                            دانلود
                                        </button>
                                        
                                        <button wire:click="togglePublic({{ $selectedFile->id }})" 
                                                class="{{ $selectedFile->is_public ? 'bg-orange-600 hover:bg-orange-700' : 'bg-blue-600 hover:bg-blue-700' }} text-white px-3 py-2 rounded-lg text-sm transition-colors">
                                            {{ $selectedFile->is_public ? 'خصوصی کردن' : 'عمومی کردن' }}
                                        </button>
                                        
                                        <button wire:click="deleteFile({{ $selectedFile->id }})" 
                                                onclick="return confirm('آیا مطمئن هستید؟')"
                                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-sm transition-colors">
                                            حذف
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Success Message -->
    @if (session()->has('message'))
        <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('message') }}
        </div>
    @endif

    <!-- Copy to Clipboard Script -->
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('copyToClipboard', (url) => {
                navigator.clipboard.writeText(url).then(() => {
                    // Show temporary success message
                    const message = document.createElement('div');
                    message.className = 'fixed bottom-4 right-4 bg-blue-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
                    message.textContent = 'لینک کپی شد!';
                    document.body.appendChild(message);
                    
                    setTimeout(() => {
                        document.body.removeChild(message);
                    }, 2000);
                });
            });
        });
    </script>
    </div>
</div>