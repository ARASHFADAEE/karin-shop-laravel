<div class="category-selector">
    <!-- Header with Search and Controls -->
    <div class="bg-gray-50 border border-gray-300 rounded-lg p-4">
        <!-- Search Input -->
        <div class="flex items-center space-x-3 space-x-reverse mb-3">
            <div class="flex-1 relative">
                <input type="text" 
                       wire:model.live.debounce.300ms="search"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="{{ $placeholder }}">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="flex items-center space-x-2 space-x-reverse">
                @if($allowMultiple)
                    <button type="button" 
                            wire:click="selectAll"
                            class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded hover:bg-blue-200">
                        انتخاب همه
                    </button>
                @endif
                
                @if(!empty($selectedCategories))
                    <button type="button" 
                            wire:click="clearAll"
                            class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded hover:bg-red-200">
                        پاک کردن
                    </button>
                    
                    <button type="button" 
                            wire:click="toggleShowSelected"
                            class="text-xs {{ $showSelected ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }} px-2 py-1 rounded hover:bg-opacity-80">
                        {{ $showSelected ? 'همه' : 'انتخاب شده' }} ({{ count($selectedCategories) }})
                    </button>
                @endif
            </div>
        </div>
        
        <!-- Stats -->
        <div class="flex items-center justify-between text-xs text-gray-600">
            <span>
                @if($showSelected && !empty($selectedCategories))
                    {{ count($selectedCategories) }} دسته انتخاب شده
                @else
                    {{ $this->filteredCategories->count() }} دسته یافت شد
                @endif
            </span>
            
            @if(!empty($search))
                <button type="button" 
                        wire:click="$set('search', '')"
                        class="text-blue-600 hover:text-blue-800">
                    پاک کردن جستجو
                </button>
            @endif
        </div>
    </div>
    
    <!-- Categories List -->
    <div class="mt-3 border border-gray-200 rounded-lg bg-white" style="max-height: {{ $maxHeight }}px; overflow-y: auto;">
        @if($showSelected && !empty($selectedCategories))
            <!-- Show Selected Categories -->
            @foreach($this->selectedCategoriesData as $category)
                <label class="flex items-center p-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0 cursor-pointer">
                    <input type="{{ $allowMultiple ? 'checkbox' : 'radio' }}" 
                           wire:model="selectedCategories" 
                           value="{{ $category->id }}"
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="mr-3 text-sm text-gray-700">{{ $category->name }}</span>
                    @if($category->parent_id)
                        <span class="text-xs text-gray-500 mr-auto">زیرمجموعه</span>
                    @endif
                    <button type="button" 
                            wire:click="removeCategory({{ $category->id }})"
                            class="mr-2 text-red-500 hover:text-red-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </label>
            @endforeach
        @else
            <!-- Show All/Filtered Categories -->
            @if($showHierarchy)
                <!-- Hierarchical View -->
                @foreach($this->categoryHierarchy as $item)
                    @include('livewire.partials.category-item', ['item' => $item])
                @endforeach
            @else
                <!-- Flat View -->
                @foreach($this->filteredCategories as $category)
                    <label class="flex items-center p-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0 cursor-pointer">
                        <input type="{{ $allowMultiple ? 'checkbox' : 'radio' }}" 
                               wire:model="selectedCategories" 
                               value="{{ $category->id }}"
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="mr-3 text-sm text-gray-700">{{ $category->name }}</span>
                        @if($category->parent_id)
                            <span class="text-xs text-gray-500 mr-auto">زیرمجموعه</span>
                        @endif
                    </label>
                @endforeach
            @endif
        @endif
        
        @if($this->filteredCategories->count() === 0)
            <div class="p-8 text-center text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 20a7.962 7.962 0 01-5.291-1.709M6.343 6.343A8 8 0 1017.657 17.657 8 8 0 006.343 6.343z"></path>
                </svg>
                <p class="text-sm">دسته‌بندی‌ای یافت نشد</p>
                @if(!empty($search))
                    <button type="button" 
                            wire:click="$set('search', '')"
                            class="mt-2 text-blue-600 hover:text-blue-800 text-sm">
                        پاک کردن جستجو
                    </button>
                @endif
            </div>
        @endif
    </div>
    
    <!-- Selected Categories Tags -->
    @if(!empty($selectedCategories) && !$showSelected)
        <div class="mt-3">
            <p class="text-sm font-medium text-gray-700 mb-2">دسته‌های انتخاب شده:</p>
            <div class="flex flex-wrap gap-2">
                @foreach($this->selectedCategoriesData as $category)
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
    
    <!-- Primary Category Selection -->
    @if($allowMultiple && !empty($selectedCategories))
        <div class="mt-3">
            <label class="block text-sm font-medium text-gray-700 mb-2">دسته‌بندی اصلی</label>
            <select wire:model="primaryCategory"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">انتخاب دسته‌بندی اصلی</option>
                @foreach($this->selectedCategoriesData as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
    @endif
</div>