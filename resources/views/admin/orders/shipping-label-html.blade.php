<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>برچسب ارسال سفارش {{ $order->order_number ?? '#' . $order->id }}</title>
    
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Print Styles -->
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                font-size: 10pt;
                line-height: 1.4;
                margin: 0;
                padding: 0;
            }
            
            .print-container {
                max-width: none;
                margin: 0;
                padding: 0;
                box-shadow: none;
                width: 100mm;
                height: 150mm;
                page-break-after: always;
            }
            
            .label-content {
                width: 100mm;
                height: 150mm;
                padding: 8mm;
                box-sizing: border-box;
            }
        }
        
        .print-container {
            font-family: 'IRANYekan', 'Tahoma', sans-serif;
            font-variation-settings: "wght" 400, "dots" 2;
        }
        
        .label-preview {
            width: 100mm;
            height: 150mm;
            transform: scale(1.5);
            transform-origin: top right;
            margin: 0 auto;
        }
        
        @media screen {
            .label-container {
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                padding: 2rem;
            }
        }
    </style>
    
    <!-- PDF Generation Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</head>
<body class="bg-gray-100">
    <!-- Action Buttons -->
    <div class="no-print fixed top-4 left-4 z-50 flex space-x-2 space-x-reverse">
        <button onclick="downloadPDF()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-lg flex items-center transition-colors">
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            دانلود PDF
        </button>
        
        <button onclick="window.print()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-lg flex items-center transition-colors">
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            چاپ
        </button>
        
        <a href="{{ route('admin.orders.show', $order) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-lg flex items-center transition-colors">
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            بازگشت
        </a>
    </div>
    
    <!-- Label Container -->
    <div class="label-container">
        <div id="shipping-label" class="print-container label-preview bg-white border-4 border-black shadow-2xl">
            <div class="label-content h-full flex flex-col">
                <!-- Header -->
                <div class="text-center border-b-2 border-black pb-2 mb-3">
                    <div class="text-lg font-bold text-black">{{ $settings->invoice_company_name ?? 'کارین شاپ' }}</div>
                    <div class="text-xs text-gray-700">
                        @if($settings && $settings->invoice_company_phone)
                            تلفن: {{ $settings->invoice_company_phone }}
                        @endif
                        @if($settings && $settings->invoice_company_email && $settings->invoice_company_phone)
                            |
                        @endif
                        @if($settings && $settings->invoice_company_email)
                            {{ $settings->invoice_company_email }}
                        @endif
                    </div>
                </div>
                
                <!-- Order Info -->
                <div class="mb-4">
                    <div class="text-center">
                        <div class="text-sm font-bold text-black mb-1">برچسب ارسال</div>
                        <div class="text-xs text-gray-700">سفارش: {{ $order->order_number ?? '#' . $order->id }}</div>
                        <div class="text-xs text-gray-700">تاریخ: {{ $order->created_at->format('Y/m/d') }}</div>
                    </div>
                </div>
                
                <!-- Customer Info -->
                <div class="border-2 border-gray-400 rounded p-2 mb-3 flex-1">
                    <div class="text-xs font-bold text-black mb-2">گیرنده:</div>
                    <div class="space-y-1">
                        <div class="text-xs font-medium text-black">{{ $order->user->name }}</div>
                        @if($order->user->phone)
                            <div class="text-xs text-gray-700">تلفن: {{ $order->user->phone }}</div>
                        @endif
                        
                        @if($order->shipping_address)
                            <div class="text-xs text-gray-700 leading-relaxed mt-2">
                                <div class="font-medium text-black mb-1">آدرس:</div>
                                <div>{{ $order->shipping_address }}</div>
                            </div>
                        @else
                            <div class="text-xs text-gray-700 leading-relaxed mt-2">
                                <div class="font-medium text-black mb-1">آدرس:</div>
                                <div>آدرس ارسال در سیستم ثبت نشده است</div>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Package Info -->
                <div class="border-2 border-gray-400 rounded p-2 mb-3">
                    <div class="text-xs font-bold text-black mb-2">اطلاعات بسته:</div>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="text-gray-600">تعداد آیتم:</span>
                            <span class="font-medium text-black">{{ $order->orderItems->count() }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">وزن:</span>
                            <span class="font-medium text-black">{{ $order->orderItems->sum('quantity') * 0.5 }} کیلو</span>
                        </div>
                    </div>
                    
                    <!-- Items List -->
                    <div class="mt-2">
                        <div class="text-xs font-medium text-black mb-1">محصولات:</div>
                        <div class="space-y-1">
                            @foreach($order->orderItems->take(3) as $item)
                                <div class="text-xs text-gray-700 flex justify-between">
                                    <span class="truncate flex-1 ml-2">{{ Str::limit($item->product->name, 25) }}</span>
                                    <span class="font-medium">×{{ $item->quantity }}</span>
                                </div>
                            @endforeach
                            @if($order->orderItems->count() > 3)
                                <div class="text-xs text-gray-500">و {{ $order->orderItems->count() - 3 }} آیتم دیگر...</div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Status & Notes -->
                <div class="border-2 border-gray-400 rounded p-2 mb-3">
                    <div class="flex justify-between items-center mb-2">
                        <div class="text-xs font-bold text-black">وضعیت:</div>
                        <div class="px-2 py-1 rounded text-xs font-medium
                            @if($order->status === 'pending') bg-yellow-200 text-yellow-800
                            @elseif($order->status === 'processing') bg-blue-200 text-blue-800
                            @elseif($order->status === 'shipped') bg-purple-200 text-purple-800
                            @elseif($order->status === 'delivered') bg-green-200 text-green-800
                            @elseif($order->status === 'cancelled') bg-red-200 text-red-800
                            @else bg-gray-200 text-gray-800
                            @endif">
                            @switch($order->status)
                                @case('pending') در انتظار @break
                                @case('processing') پردازش @break
                                @case('shipped') ارسال شده @break
                                @case('delivered') تحویل @break
                                @case('cancelled') لغو شده @break
                                @default {{ $order->status }}
                            @endswitch
                        </div>
                    </div>
                    
                    @if($order->notes)
                        <div class="text-xs text-gray-700">
                            <span class="font-medium text-black">یادداشت:</span>
                            {{ Str::limit($order->notes, 50) }}
                        </div>
                    @endif
                </div>
                
                <!-- Footer -->
                <div class="border-t-2 border-black pt-2 mt-auto">
                    <div class="flex justify-between items-center text-xs">
                        <div class="text-gray-700">{{ now()->format('Y/m/d H:i') }}</div>
                        <div class="font-bold text-black">{{ $settings->invoice_company_name ?? 'کارین شاپ' }}</div>
                    </div>
                    <div class="text-center text-xs text-gray-600 mt-1">
                        لطفاً این برچسب را روی بسته بچسبانید
                    </div>
                    @if($settings && $settings->invoice_company_website)
                        <div class="text-center text-xs text-gray-500 mt-1">
                            {{ $settings->invoice_company_website }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function downloadPDF() {
            const element = document.getElementById('shipping-label');
            const opt = {
                margin: 0,
                filename: 'shipping-label-order-{{ $order->id }}.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { 
                    scale: 3,
                    useCORS: true,
                    letterRendering: true,
                    width: 378, // 100mm in pixels at 96 DPI
                    height: 567 // 150mm in pixels at 96 DPI
                },
                jsPDF: { 
                    unit: 'mm', 
                    format: [100, 150], 
                    orientation: 'portrait',
                    putOnlyUsedFonts: true
                }
            };
            
            html2pdf().set(opt).from(element).save();
        }
        
        // Auto-focus for better UX
        document.addEventListener('DOMContentLoaded', function() {
            // Add any initialization code here
        });
    </script>
</body>
</html>