<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-900">مدیریت نظرات</h2>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">جستجو</label>
                <input type="text" wire:model.live="search" placeholder="نظر، کاربر یا محصول..." 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">وضعیت</label>
                <select wire:model.live="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">همه</option>
                    <option value="pending">در انتظار</option>
                    <option value="approved">تایید شده</option>
                    <option value="rejected">رد شده</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">امتیاز</label>
                <select wire:model.live="rating" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">همه</option>
                    <option value="5">5 ستاره</option>
                    <option value="4">4 ستاره</option>
                    <option value="3">3 ستاره</option>
                    <option value="2">2 ستاره</option>
                    <option value="1">1 ستاره</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">تعداد در صفحه</label>
                <select wire:model.live="perPage" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Reviews List -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">لیست نظرات ({{ $reviews->total() }})</h3>
        </div>
        
        <div class="divide-y divide-gray-200">
            @forelse($reviews as $review)
                <div class="p-6" x-data="{ showReply: false, replyText: '{{ $review->admin_reply }}' }">
                    <!-- Review Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center space-x-4 space-x-reverse">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium text-gray-700">
                                        {{ substr($review->user->name, 0, 1) }}
                                    </span>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">{{ $review->user->name }}</h4>
                                <p class="text-sm text-gray-500">{{ $review->created_at->format('Y/m/d H:i') }}</p>
                            </div>
                        </div>
                        
                        <!-- Status Badge -->
                        <div class="flex items-center space-x-2 space-x-reverse">
                            @if($review->status === 'approved')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    تایید شده
                                </span>
                            @elseif($review->status === 'rejected')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    رد شده
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    در انتظار
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600">محصول: 
                            <span class="font-medium text-gray-900">{{ $review->product->name }}</span>
                        </p>
                    </div>

                    <!-- Rating -->
                    <div class="flex items-center mb-3">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $review->rating)
                                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            @else
                                <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            @endif
                        @endfor
                        <span class="mr-2 text-sm text-gray-600">({{ $review->rating }}/5)</span>
                    </div>

                    <!-- Comment -->
                    <div class="mb-4">
                        <p class="text-gray-700">{{ $review->comment }}</p>
                    </div>

                    <!-- Admin Reply -->
                    @if($review->admin_reply)
                        <div class="mb-4 p-3 bg-blue-50 border-r-4 border-blue-400 rounded">
                            <div class="flex items-center mb-2">
                                <svg class="w-4 h-4 text-blue-600 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <span class="text-sm font-medium text-blue-800">پاسخ ادمین</span>
                                <span class="text-xs text-blue-600 mr-2">{{ $review->replied_at->format('Y/m/d H:i') }}</span>
                            </div>
                            <p class="text-blue-700">{{ $review->admin_reply }}</p>
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                        <div class="flex items-center space-x-2 space-x-reverse">
                            @if($review->status !== 'approved')
                                <button wire:click="approve({{ $review->id }})" 
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700">
                                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    تایید
                                </button>
                            @endif
                            
                            @if($review->status !== 'rejected')
                                <button wire:click="reject({{ $review->id }})" 
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700">
                                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    رد
                                </button>
                            @endif
                            
                            <button @click="showReply = !showReply" 
                                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                پاسخ
                            </button>
                        </div>
                        
                        <button wire:click="delete({{ $review->id }})" 
                                onclick="return confirm('آیا مطمئن هستید؟')"
                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700">
                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            حذف
                        </button>
                    </div>

                    <!-- Reply Form -->
                    <div x-show="showReply" x-transition class="mt-4 p-4 bg-gray-50 rounded-lg">
                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-gray-700">پاسخ شما:</label>
                            <textarea x-model="replyText" 
                                      rows="3" 
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="پاسخ خود را بنویسید..."></textarea>
                            <div class="flex justify-end space-x-2 space-x-reverse">
                                <button @click="showReply = false" 
                                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                    انصراف
                                </button>
                                <button @click="$wire.reply({{ $review->id }}, replyText); showReply = false" 
                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                    ارسال پاسخ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">هیچ نظری یافت نشد</h3>
                    <p class="text-gray-500">نظری با فیلترهای انتخابی وجود ندارد.</p>
                </div>
            @endforelse
        </div>
        
        @if($reviews->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>

    <!-- Statistics -->
    @if($reviews->count() > 0)
        <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="text-sm font-medium text-gray-500">کل نظرات</div>
                <div class="text-2xl font-bold text-gray-900">{{ $reviews->total() }}</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="text-sm font-medium text-gray-500">تایید شده</div>
                <div class="text-2xl font-bold text-green-600">{{ $reviews->where('status', 'approved')->count() }}</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="text-sm font-medium text-gray-500">در انتظار</div>
                <div class="text-2xl font-bold text-yellow-600">{{ $reviews->where('status', 'pending')->count() }}</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="text-sm font-medium text-gray-500">رد شده</div>
                <div class="text-2xl font-bold text-red-600">{{ $reviews->where('status', 'rejected')->count() }}</div>
            </div>
        </div>
    @endif
</div>
