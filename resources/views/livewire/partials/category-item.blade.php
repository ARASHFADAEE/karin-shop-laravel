<div>
    <!-- Category Item -->
    <label class="flex items-center p-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0 cursor-pointer"
           style="padding-right: {{ ($item['level'] * 20) + 12 }}px;">
        
        <!-- Indentation indicator -->
        @if($item['level'] > 0)
            <div class="flex items-center mr-2">
                @for($i = 0; $i < $item['level']; $i++)
                    <div class="w-4 h-px bg-gray-300 mr-1"></div>
                @endfor
                <svg class="w-3 h-3 text-gray-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
        @endif
        
        <!-- Checkbox/Radio -->
        <input type="{{ $allowMultiple ? 'checkbox' : 'radio' }}" 
               wire:model="selectedCategories" 
               value="{{ $item['category']->id }}"
               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
        
        <!-- Category Name -->
        <span class="mr-3 text-sm text-gray-700 {{ $item['level'] > 0 ? 'font-normal' : 'font-medium' }}">
            {{ $item['category']->name }}
        </span>
        
        <!-- Level indicator -->
        @if($item['level'] > 0)
            <span class="text-xs text-gray-500 mr-auto">
                سطح {{ $item['level'] + 1 }}
            </span>
        @endif
        
        <!-- Children count -->
        @if(count($item['children']) > 0)
            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full mr-2">
                {{ count($item['children']) }} زیرمجموعه
            </span>
        @endif
    </label>
    
    <!-- Children -->
    @if(count($item['children']) > 0)
        @foreach($item['children'] as $child)
            @include('livewire.partials.category-item', ['item' => $child])
        @endforeach
    @endif
</div>