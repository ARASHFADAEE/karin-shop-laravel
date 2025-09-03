<div>
    @section('title', 'تیکت‌های پشتیبانی')

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold">تیکت‌های پشتیبانی</h2>
            <div class="flex space-x-2 space-x-reverse">
                <button wire:click="createTicket" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    تیکت جدید
                </button>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-6">
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-blue-600">{{ $stats['total'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">کل تیکت‌ها</div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-green-600">{{ $stats['open'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">باز</div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-gray-600">{{ $stats['closed'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">بسته</div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-orange-600">{{ $stats['unassigned'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">بدون واگذاری</div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-red-600">{{ $stats['overdue'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">عقب افتاده</div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-purple-600">{{ $stats['my_tickets'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">تیکت‌های من</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <input type="text" wire:model.live="search" placeholder="جستجو..."
                       class="border border-gray-300 rounded-lg px-3 py-2">
                <select wire:model.live="filterStatus" class="border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">همه وضعیت‌ها</option>
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <select wire:model.live="filterPriority" class="border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">همه اولویت‌ها</option>
                    @foreach($priorityOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <select wire:model.live="filterCategory" class="border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">همه دسته‌ها</option>
                    @foreach($categoryOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <button wire:click="clearFilters" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                    پاک کردن فیلترها
                </button>
            </div>
        </div>

        <!-- Tickets Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            شماره تیکت
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            موضوع
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            کاربر
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            وضعیت
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            اولویت
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            تاریخ ایجاد
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            عملیات
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($tickets as $ticket)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $ticket->ticket_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $ticket->subject }}</div>
                                <div class="text-sm text-gray-500">{{ Str::limit($ticket->description, 50) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $ticket->user->name ?? 'نامشخص' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($ticket->status === 'open') bg-green-100 text-green-800
                                    @elseif($ticket->status === 'closed') bg-gray-100 text-gray-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ $ticket->getStatusLabel() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($ticket->priority === 'urgent') bg-red-100 text-red-800
                                    @elseif($ticket->priority === 'high') bg-orange-100 text-orange-800
                                    @elseif($ticket->priority === 'medium') bg-yellow-100 text-yellow-800
                                    @else bg-green-100 text-green-800 @endif">
                                    {{ $ticket->getPriorityLabel() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $ticket->created_at->format('Y/m/d H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.tickets.show', $ticket) }}"
                                   class="text-blue-600 hover:text-blue-900 ml-3">مشاهده</a>
                                @if($ticket->isOpen())
                                    <button wire:click="assignToMe({{ $ticket->id }})"
                                            class="text-green-600 hover:text-green-900 ml-3">واگذاری به من</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                هیچ تیکتی یافت نشد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $tickets->links() }}
        </div>
    </div>
</div>
