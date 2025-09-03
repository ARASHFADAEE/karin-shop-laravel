<div>
    @section('title', 'جزئیات تیکت #' . $ticket->ticket_number)

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Ticket Details -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h2 class="text-xl font-semibold">{{ $ticket->subject }}</h2>
                        <p class="text-gray-600 mt-1">تیکت #{{ $ticket->ticket_number }}</p>
                    </div>
                    <div class="flex space-x-2 space-x-reverse">
                        @if($ticket->isOpen())
                            <button wire:click="markAsResolved"
                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                                حل شده
                            </button>
                            <button wire:click="closeTicket"
                                    class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded text-sm">
                                بستن
                            </button>
                        @else
                            <button wire:click="reopenTicket"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                                بازگشایی
                            </button>
                        @endif
                    </div>
                </div>

                <div class="border-t pt-4">
                    <p class="text-gray-700">{{ $ticket->description }}</p>
                </div>
            </div>

            <!-- Messages -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">پیام‌ها</h3>

                <div class="space-y-4 mb-6">
                    @forelse($messages as $message)
                        <div class="border rounded-lg p-4 {{ $message->isFromAdmin() ? 'bg-blue-50 border-blue-200' : 'bg-gray-50' }}">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    <span class="font-medium">{{ $message->user->name ?? 'سیستم' }}</span>
                                    @if($message->isFromAdmin())
                                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">ادمین</span>
                                    @endif
                                    @if($message->is_internal)
                                        <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">داخلی</span>
                                    @endif
                                </div>
                                <span class="text-sm text-gray-500">{{ $message->created_at->format('Y/m/d H:i') }}</span>
                            </div>
                            <div class="text-gray-700">{!! $message->getFormattedMessage() !!}</div>

                            @if($message->hasAttachments())
                                <div class="mt-3">
                                    <h5 class="text-sm font-medium text-gray-700 mb-2">فایل‌های ضمیمه:</h5>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($message->attachments as $index => $attachment)
                                            <button wire:click="downloadAttachment({{ $message->id }}, {{ $index }})"
                                                    class="bg-gray-100 hover:bg-gray-200 px-3 py-1 rounded text-sm">
                                                📎 {{ $attachment['name'] }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">هیچ پیامی یافت نشد.</p>
                    @endforelse
                </div>

                <!-- New Message Form -->
                @if($ticket->isOpen())
                    <form wire:submit="sendMessage" class="border-t pt-4">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">پیام جدید</label>
                            <textarea wire:model="newMessage" rows="4"
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="پیام خود را بنویسید..."></textarea>
                            @error('newMessage') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">فایل‌های ضمیمه</label>
                            <input type="file" wire:model="attachments" multiple
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        </div>

                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="isInternal" class="rounded">
                                <span class="mr-2 text-sm text-gray-700">پیام داخلی (فقط برای ادمین‌ها)</span>
                            </label>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                                ارسال پیام
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Ticket Info -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">اطلاعات تیکت</h3>

                <div class="space-y-3">
                    <div>
                        <span class="text-sm font-medium text-gray-700">وضعیت:</span>
                        <span class="mr-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($ticket->status === 'open') bg-green-100 text-green-800
                            @elseif($ticket->status === 'closed') bg-gray-100 text-gray-800
                            @else bg-yellow-100 text-yellow-800 @endif">
                            {{ $ticket->getStatusLabel() }}
                        </span>
                    </div>

                    <div>
                        <span class="text-sm font-medium text-gray-700">اولویت:</span>
                        <span class="mr-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($ticket->priority === 'urgent') bg-red-100 text-red-800
                            @elseif($ticket->priority === 'high') bg-orange-100 text-orange-800
                            @elseif($ticket->priority === 'medium') bg-yellow-100 text-yellow-800
                            @else bg-green-100 text-green-800 @endif">
                            {{ $ticket->getPriorityLabel() }}
                        </span>
                    </div>

                    <div>
                        <span class="text-sm font-medium text-gray-700">دسته‌بندی:</span>
                        <span class="mr-2 text-sm text-gray-900">{{ $ticket->getCategoryLabel() }}</span>
                    </div>

                    <div>
                        <span class="text-sm font-medium text-gray-700">کاربر:</span>
                        <span class="mr-2 text-sm text-gray-900">{{ $ticket->user->name ?? 'نامشخص' }}</span>
                    </div>

                    @if($ticket->assignedTo)
                        <div>
                            <span class="text-sm font-medium text-gray-700">واگذار شده به:</span>
                            <span class="mr-2 text-sm text-gray-900">{{ $ticket->assignedTo->name }}</span>
                        </div>
                    @endif

                    <div>
                        <span class="text-sm font-medium text-gray-700">تاریخ ایجاد:</span>
                        <span class="mr-2 text-sm text-gray-900">{{ $ticket->created_at->format('Y/m/d H:i') }}</span>
                    </div>

                    @if($ticket->resolved_at)
                        <div>
                            <span class="text-sm font-medium text-gray-700">تاریخ حل:</span>
                            <span class="mr-2 text-sm text-gray-900">{{ $ticket->resolved_at->format('Y/m/d H:i') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">عملیات سریع</h3>

                <div class="space-y-2">
                    @if(!$ticket->assigned_to)
                        <button wire:click="assignToMe"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                            واگذاری به من
                        </button>
                    @endif

                    @if($ticket->isOpen() && !$ticket->is_escalated)
                        <button wire:click="escalateTicket"
                                class="w-full bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm">
                            ارجاع
                        </button>
                    @endif

                    <button wire:click="toggleEdit"
                            class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">
                        {{ $editingTicket ? 'انصراف' : 'ویرایش تیکت' }}
                    </button>
                </div>
            </div>

            <!-- Edit Form -->
            @if($editingTicket)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">ویرایش تیکت</h3>

                    <form wire:submit="updateTicket">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">موضوع</label>
                                <input type="text" wire:model="editSubject"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">اولویت</label>
                                <select wire:model="editPriority"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                    @foreach($priorityOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">وضعیت</label>
                                <select wire:model="editStatus"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                    @foreach($statusOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">واگذاری</label>
                                <select wire:model="editAssignee"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                    <option value="">بدون واگذاری</option>
                                    @foreach($admins as $admin)
                                        <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit"
                                    class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                                ذخیره تغییرات
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
