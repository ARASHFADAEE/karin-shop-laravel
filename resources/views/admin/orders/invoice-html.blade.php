<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>فاکتور سفارش {{ $order->order_number ?? '#' . $order->id }}</title>
    
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Print Styles -->
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                font-size: 12pt;
                line-height: 1.5;
            }
            
            .print-container {
                max-width: none;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }
        }
        
        .print-container {
            font-family: 'IRANYekan', 'Tahoma', sans-serif;
            font-variation-settings: "wght" 400, "dots" 2;
        }
        
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 70px;
            color: rgba(37, 99, 235, 0.07);
            font-weight: 700;
            z-index: -1;
            pointer-events: none;
            white-space: nowrap;
        }
    </style>
    
    <!-- PDF Generation Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</head>
<body class="bg-gray-50">
    <!-- Watermark -->
    @if($settings && $settings->invoice_show_watermark)
    <div class="watermark">{{ $settings->invoice_watermark_text ?? 'کارین شاپ' }}</div>
    @endif
    
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
    
    <!-- Invoice Container -->
    <div class="min-h-screen py-8">
        <div class="max-w-4xl mx-auto">
            <div id="invoice-content" class="print-container bg-white shadow-xl rounded-lg overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r text-white p-8" style="background: linear-gradient(135deg, {{ $settings->invoice_primary_color ?? '#2563eb' }}, {{ $settings->invoice_secondary_color ?? '#1e40af' }})">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center">
                            @if($settings && $settings->invoice_logo_url)
                                <img src="{{ $settings->invoice_logo_url }}" alt="لوگو" class="w-16 h-16 ml-4 rounded-lg bg-white p-2">
                            @endif
                            <div>
                                <h1 class="text-3xl font-bold mb-2">{{ $settings->invoice_company_name ?? 'کارین شاپ' }}</h1>
                                <div class="text-blue-100 space-y-1">
                                    @if($settings && $settings->invoice_company_phone)
                                        <p>📞 {{ $settings->invoice_company_phone }}</p>
                                    @endif
                                    @if($settings && $settings->invoice_company_email)
                                        <p>📧 {{ $settings->invoice_company_email }}</p>
                                    @endif
                                    @if($settings && $settings->invoice_company_website)
                                        <p>🌐 {{ $settings->invoice_company_website }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="text-left bg-white bg-opacity-10 rounded-lg p-4">
                            <h2 class="text-2xl font-bold mb-2">فاکتور فروش</h2>
                            <p class="text-blue-100">شماره: {{ $order->order_number ?? '#' . $order->id }}</p>
                            <p class="text-blue-100">تاریخ: {{ $order->created_at->format('Y/m/d') }}</p>
                            <p class="text-blue-100">ساعت: {{ $order->created_at->format('H:i') }}</p>
                        </div>
                    </div>
                    
                    @if($settings && $settings->invoice_company_address)
                    <div class="mt-4 pt-4 border-t border-white border-opacity-20">
                        <p class="text-blue-100">📍 {{ $settings->invoice_company_address }}</p>
                    </div>
                    @endif
                </div>
                
                <!-- Customer & Order Info -->
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <!-- Customer Info -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <svg class="w-5 h-5 ml-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                اطلاعات مشتری
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">نام:</span>
                                    <span class="font-medium">{{ $order->user->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">ایمیل:</span>
                                    <span class="font-medium">{{ $order->user->email }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">تلفن:</span>
                                    <span class="font-medium">{{ $order->user->phone ?? 'ثبت نشده' }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Order Info -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <svg class="w-5 h-5 ml-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                اطلاعات سفارش
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">شماره سفارش:</span>
                                    <span class="font-medium">{{ $order->order_number ?? '#' . $order->id }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">تاریخ ثبت:</span>
                                    <span class="font-medium">{{ $order->created_at->format('Y/m/d H:i') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">وضعیت:</span>
                                    <span class="px-3 py-1 rounded-full text-xs font-medium
                                        @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                                        @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                                        @elseif($order->status === 'delivered') bg-green-100 text-green-800
                                        @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        @switch($order->status)
                                            @case('pending') در انتظار پردازش @break
                                            @case('processing') در حال پردازش @break
                                            @case('shipped') ارسال شده @break
                                            @case('delivered') تحویل داده شده @break
                                            @case('cancelled') لغو شده @break
                                            @default {{ $order->status }}
                                        @endswitch
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Items -->
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 ml-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            محصولات سفارش
                        </h3>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse border border-gray-300">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="border border-gray-300 px-4 py-3 text-right font-bold">محصول</th>
                                        <th class="border border-gray-300 px-4 py-3 text-center font-bold">قیمت واحد</th>
                                        <th class="border border-gray-300 px-4 py-3 text-center font-bold">تعداد</th>
                                        <th class="border border-gray-300 px-4 py-3 text-center font-bold">قیمت کل</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->orderItems as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="border border-gray-300 px-4 py-3">
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $item->product->name }}</div>
                                                @if($item->product->sku)
                                                    <div class="text-sm text-gray-500">کد محصول: {{ $item->product->sku }}</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="border border-gray-300 px-4 py-3 text-center">{{ number_format($item->price) }} تومان</td>
                                        <td class="border border-gray-300 px-4 py-3 text-center">{{ $item->quantity }}</td>
                                        <td class="border border-gray-300 px-4 py-3 text-center font-bold">{{ number_format($item->price * $item->quantity) }} تومان</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Order Summary -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">خلاصه سفارش</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">تعداد کل آیتم‌ها:</span>
                                <span class="font-medium">{{ $order->orderItems->count() }} قلم</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">تعداد کل محصولات:</span>
                                <span class="font-medium">{{ $order->orderItems->sum('quantity') }} عدد</span>
                            </div>
                            <div class="border-t border-gray-300 pt-3">
                                <div class="flex justify-between text-lg font-bold">
                                    <span>مبلغ کل:</span>
                                    <span class="text-blue-600">{{ number_format($order->total_amount) }} تومان</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Info -->
                    @if($order->payment)
                    <div class="mt-8 bg-green-50 rounded-lg p-6">
                        <h3 class="text-lg font-bold text-green-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 ml-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            اطلاعات پرداخت
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if($order->payment->transaction_id)
                            <div class="flex justify-between">
                                <span class="text-gray-600">شماره تراکنش:</span>
                                <span class="font-medium">{{ $order->payment->transaction_id }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-gray-600">تاریخ پرداخت:</span>
                                <span class="font-medium">{{ $order->payment->created_at->format('Y/m/d H:i') }}</span>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Terms and Conditions -->
                    @if($settings && $settings->invoice_terms)
                    <div class="mt-8 bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">شرایط و قوانین</h3>
                        <p class="text-gray-700 leading-relaxed">{{ $settings->invoice_terms }}</p>
                    </div>
                    @endif
                    
                    <!-- Footer -->
                    <div class="mt-8 text-center text-gray-600 text-sm border-t border-gray-200 pt-6">
                        <p class="mb-2">{{ $settings->invoice_footer_text ?? 'با تشکر از خرید شما' }} | {{ $settings->invoice_company_name ?? 'کارین شاپ' }}</p>
                        <p class="mb-2">این فاکتور به صورت الکترونیکی تولید شده و نیازی به مهر و امضا ندارد.</p>
                        <p>تاریخ تولید: {{ now()->format('Y/m/d H:i:s') }}</p>
                        @if($settings && $settings->invoice_company_website)
                            <p class="mt-2">🌐 {{ $settings->invoice_company_website }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function downloadPDF() {
            const element = document.getElementById('invoice-content');
            const opt = {
                margin: 0.5,
                filename: 'invoice-order-{{ $order->id }}.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { 
                    scale: 2,
                    useCORS: true,
                    letterRendering: true
                },
                jsPDF: { 
                    unit: 'in', 
                    format: 'a4', 
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