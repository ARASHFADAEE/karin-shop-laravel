<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>فاکتور سفارش {{ $order->order_number ?? '#' . $order->id }}</title>
    
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Enhanced Print & Responsive Styles -->
    <style>
        :root {
            --primary-color: {{ $settings->invoice_primary_color ?? '#2563eb' }};
            --secondary-color: {{ $settings->invoice_secondary_color ?? '#1e40af' }};
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --border-color: #e5e7eb;
            --bg-light: #f9fafb;
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'IRANYekan', 'Tahoma', 'Arial Unicode MS', sans-serif;
            font-variation-settings: "wght" 400, "dots" 2;
            line-height: 1.6;
            color: var(--text-primary);
            background: #f3f4f6;
            margin: 0;
            padding: 0;
        }
        
        .invoice-container {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-radius: 12px;
            overflow: hidden;
            position: relative;
        }
        
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: clamp(3rem, 8vw, 6rem);
            color: rgba(0, 0, 0, 0.03);
            font-weight: 900;
            z-index: 1;
            pointer-events: none;
            user-select: none;
            white-space: nowrap;
        }
        
        .invoice-content {
            position: relative;
            z-index: 2;
            padding: 2rem;
        }
        
        .invoice-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem;
            margin: -2rem -2rem 2rem -2rem;
            border-radius: 12px 12px 0 0;
        }
        
        .company-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .company-logo {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 8px;
            backdrop-filter: blur(10px);
        }
        
        .company-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 8px;
        }
        
        .company-details h1 {
            font-size: 2rem;
            font-weight: 800;
            margin: 0 0 0.5rem 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .company-contact {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .invoice-meta {
            background: rgba(255, 255, 255, 0.1);
            padding: 1.5rem;
            border-radius: 12px;
            backdrop-filter: blur(10px);
            text-align: center;
        }
        
        .invoice-meta h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0 0 1rem 0;
        }
        
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 0.5rem;
            font-size: 0.9rem;
        }
        
        .info-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }
        
        .info-card {
            background: var(--bg-light);
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .info-card:hover {
            border-color: var(--primary-color);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1);
        }
        
        .info-card h3 {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.1rem;
            font-weight: 700;
            margin: 0 0 1rem 0;
            color: var(--primary-color);
        }
        
        .info-card .icon {
            width: 24px;
            height: 24px;
            color: var(--primary-color);
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--border-color);
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            color: var(--text-secondary);
            font-weight: 500;
        }
        
        .info-value {
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .items-section {
            margin: 2rem 0;
        }
        
        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0 0 1rem 0;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary-color);
            display: inline-block;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        .items-table th {
            background: var(--primary-color);
            color: white;
            padding: 1rem;
            text-align: right;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .items-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            vertical-align: top;
        }
        
        .items-table tr:last-child td {
            border-bottom: none;
        }
        
        .items-table tr:nth-child(even) {
            background: var(--bg-light);
        }
        
        .product-info {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        
        .product-name {
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .product-sku {
            font-size: 0.8rem;
            color: var(--text-secondary);
        }
        
        .price-cell {
            text-align: center;
            font-weight: 600;
        }
        
        .total-section {
            background: linear-gradient(135deg, var(--bg-light), white);
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            margin: 2rem 0;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border-color);
        }
        
        .total-row:last-child {
            border-bottom: none;
            border-top: 2px solid var(--primary-color);
            margin-top: 1rem;
            padding-top: 1rem;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .payment-info {
            background: linear-gradient(135deg, #ecfdf5, #f0fdf4);
            border: 2px solid #10b981;
            border-radius: 12px;
            padding: 1.5rem;
            margin: 2rem 0;
        }
        
        .payment-info h3 {
            color: #065f46;
            margin: 0 0 1rem 0;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .terms-section {
            background: var(--bg-light);
            border-radius: 12px;
            padding: 1.5rem;
            margin: 2rem 0;
        }
        
        .footer {
            text-align: center;
            padding: 2rem;
            margin: 2rem -2rem -2rem -2rem;
            background: var(--bg-light);
            border-top: 2px solid var(--border-color);
            color: var(--text-secondary);
            font-size: 0.9rem;
        }
        
        .footer-links {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }
        
        .footer-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .invoice-content {
                padding: 1rem;
            }
            
            .invoice-header {
                padding: 1.5rem;
                margin: -1rem -1rem 1.5rem -1rem;
            }
            
            .company-info {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }
            
            .company-details h1 {
                font-size: 1.5rem;
            }
            
            .company-contact {
                justify-content: center;
                font-size: 0.8rem;
            }
            
            .info-section {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .items-table {
                font-size: 0.8rem;
            }
            
            .items-table th,
            .items-table td {
                padding: 0.5rem;
            }
            
            .meta-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 480px) {
            .invoice-container {
                margin: 0;
                border-radius: 0;
            }
            
            .company-contact {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .items-table {
                font-size: 0.7rem;
            }
            
            .watermark {
                font-size: 2rem;
            }
        }
        
        /* Print Optimization */
        @media print {
            body {
                background: white !important;
                font-size: 10pt;
                line-height: 1.4;
                margin: 0;
                padding: 0;
            }
            
            .no-print {
                display: none !important;
            }
            
            .invoice-container {
                max-width: none;
                margin: 0;
                box-shadow: none;
                border-radius: 0;
                page-break-inside: avoid;
            }
            
            .invoice-content {
                padding: 15mm;
            }
            
            .invoice-header {
                margin: -15mm -15mm 10mm -15mm;
                border-radius: 0;
                page-break-inside: avoid;
            }
            
            .info-section {
                page-break-inside: avoid;
            }
            
            .items-table {
                page-break-inside: auto;
            }
            
            .items-table tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            
            .total-section {
                page-break-inside: avoid;
            }
            
            .footer {
                margin: 10mm -15mm -15mm -15mm;
                page-break-inside: avoid;
            }
            
            .watermark {
                opacity: 0.8;
            }
            
            /* A5 specific adjustments */
            @page {
                size: A5;
                margin: 10mm;
            }
            
            .invoice-content {
                padding: 5mm;
            }
            
            .invoice-header {
                margin: -5mm -5mm 5mm -5mm;
                padding: 10mm;
            }
            
            .company-details h1 {
                font-size: 14pt;
            }
            
            .section-title {
                font-size: 11pt;
            }
            
            .items-table th,
            .items-table td {
                padding: 3mm;
                font-size: 8pt;
            }
            
            .footer {
                margin: 5mm -5mm -5mm -5mm;
                padding: 5mm;
                font-size: 7pt;
            }
        }
        
        /* Animation for better UX */
        .invoice-container {
            animation: fadeInUp 0.6s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .info-card,
        .items-table,
        .total-section {
            animation: fadeIn 0.8s ease-out;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
    </style>
    
    <!-- PDF Generation Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</head>
<body>
    <!-- Action Buttons -->
    <div class="no-print fixed top-4 left-4 z-50 flex space-x-2 space-x-reverse">
        <button onclick="downloadPDF()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-lg flex items-center transition-all duration-300 hover:scale-105">
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            دانلود PDF
        </button>
        
        <button onclick="window.print()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-lg flex items-center transition-all duration-300 hover:scale-105">
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            چاپ
        </button>
        
        <a href="{{ route('admin.orders.show', $order) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-lg flex items-center transition-all duration-300 hover:scale-105">
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            بازگشت
        </a>
    </div>
    
    <!-- Invoice Container -->
    <div class="min-h-screen py-8">
        <div class="invoice-container" id="invoice-content">
            <!-- Watermark -->
            @if($settings && $settings->invoice_show_watermark)
            <div class="watermark">{{ $settings->invoice_watermark_text ?? 'کارین شاپ' }}</div>
            @endif
            
            <div class="invoice-content">
                <!-- Header -->
                <div class="invoice-header">
                    <div class="company-info">
                        @if($settings && $settings->invoice_logo_url)
                        <div class="company-logo">
                            <img src="{{ $settings->invoice_logo_url }}" alt="لوگو شرکت">
                        </div>
                        @endif
                        <div class="company-details">
                            <h1>{{ $settings->invoice_company_name ?? 'کارین شاپ' }}</h1>
                            <div class="company-contact">
                                @if($settings && $settings->invoice_company_phone)
                                <div class="contact-item">
                                    <span>📞</span>
                                    <span>{{ $settings->invoice_company_phone }}</span>
                                </div>
                                @endif
                                @if($settings && $settings->invoice_company_email)
                                <div class="contact-item">
                                    <span>📧</span>
                                    <span>{{ $settings->invoice_company_email }}</span>
                                </div>
                                @endif
                                @if($settings && $settings->invoice_company_website)
                                <div class="contact-item">
                                    <span>🌐</span>
                                    <span>{{ $settings->invoice_company_website }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="invoice-meta">
                        <h2>فاکتور فروش</h2>
                        <div class="meta-grid">
                            <div>
                                <strong>شماره:</strong><br>
                                {{ $order->order_number ?? '#' . $order->id }}
                            </div>
                            <div>
                                <strong>تاریخ:</strong><br>
                                {{ $order->created_at->format('Y/m/d') }}
                            </div>
                            <div>
                                <strong>ساعت:</strong><br>
                                {{ $order->created_at->format('H:i') }}
                            </div>
                        </div>
                    </div>
                    
                    @if($settings && $settings->invoice_company_address)
                    <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.2);">
                        <div class="contact-item">
                            <span>📍</span>
                            <span>{{ $settings->invoice_company_address }}</span>
                        </div>
                    </div>
                    @endif
                </div>
                
                <!-- Customer & Order Info -->
                <div class="info-section">
                    <!-- Customer Info -->
                    <div class="info-card">
                        <h3>
                            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            اطلاعات مشتری
                        </h3>
                        <div class="info-item">
                            <span class="info-label">نام و نام خانوادگی:</span>
                            <span class="info-value">{{ $order->user->name }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">ایمیل:</span>
                            <span class="info-value">{{ $order->user->email }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">شماره تماس:</span>
                            <span class="info-value">{{ $order->user->phone ?? 'ثبت نشده' }}</span>
                        </div>
                        @if($order->shipping_address)
                        <div class="info-item">
                            <span class="info-label">آدرس ارسال:</span>
                            <span class="info-value">{{ Str::limit($order->shipping_address, 50) }}</span>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Order Info -->
                    <div class="info-card">
                        <h3>
                            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            اطلاعات سفارش
                        </h3>
                        <div class="info-item">
                            <span class="info-label">شماره سفارش:</span>
                            <span class="info-value">{{ $order->order_number ?? '#' . $order->id }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">تاریخ ثبت:</span>
                            <span class="info-value">{{ $order->created_at->format('Y/m/d H:i') }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">تعداد اقلام:</span>
                            <span class="info-value">{{ $order->orderItems->sum('quantity') }} عدد</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">وضعیت سفارش:</span>
                            <span class="status-badge
                                @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                                @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                                @elseif($order->status === 'delivered') bg-green-100 text-green-800
                                @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                @switch($order->status)
                                    @case('pending') در انتظار @break
                                    @case('processing') پردازش @break
                                    @case('shipped') ارسال شده @break
                                    @case('delivered') تحویل داده شده @break
                                    @case('cancelled') لغو شده @break
                                    @default {{ $order->status }}
                                @endswitch
                            </span>
                        </div>
                    </div>
                </div>
                    
                <!-- Order Items -->
                <div class="items-section">
                    <h3 class="section-title">جزئیات سفارش</h3>
                    <div style="overflow-x: auto;">
                        <table class="items-table">
                            <thead>
                                <tr>
                                    <th>محصول</th>
                                    <th style="text-align: center;">قیمت واحد</th>
                                    <th style="text-align: center;">تعداد</th>
                                    <th style="text-align: center;">قیمت کل</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $item)
                                <tr>
                                    <td>
                                        <div class="product-info">
                                            <div class="product-name">{{ $item->product->name }}</div>
                                            @if($item->product->sku)
                                                <div class="product-sku">کد محصول: {{ $item->product->sku }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="price-cell">{{ number_format($item->price) }} تومان</td>
                                    <td class="price-cell">{{ $item->quantity }}</td>
                                    <td class="price-cell">{{ number_format($item->price * $item->quantity) }} تومان</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Order Summary -->
                <div class="total-section">
                    <div class="total-row">
                        <span>جمع کل محصولات:</span>
                        <span>{{ number_format($order->orderItems->sum(function($item) { return $item->price * $item->quantity; })) }} تومان</span>
                    </div>
                    @if($order->shipping_cost ?? 0 > 0)
                    <div class="total-row">
                        <span>هزینه ارسال:</span>
                        <span>{{ number_format($order->shipping_cost) }} تومان</span>
                    </div>
                    @endif
                    @if($order->discount_amount ?? 0 > 0)
                    <div class="total-row">
                        <span>تخفیف:</span>
                        <span style="color: #dc2626;">-{{ number_format($order->discount_amount) }} تومان</span>
                    </div>
                    @endif
                    <div class="total-row">
                        <span>مبلغ قابل پرداخت:</span>
                        <span>{{ number_format($order->total_amount) }} تومان</span>
                    </div>
                </div>
                    
                <!-- Payment Info -->
                @if($order->payment)
                <div class="payment-info">
                    <h3>
                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        اطلاعات پرداخت
                    </h3>
                    <div class="info-section" style="margin: 1rem 0;">
                        <div class="info-item">
                            <span class="info-label">روش پرداخت:</span>
                            <span class="info-value">{{ $order->payment->payment_method }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">وضعیت پرداخت:</span>
                            <span class="info-value">{{ $order->payment->status }}</span>
                        </div>
                        @if($order->payment->transaction_id)
                        <div class="info-item">
                            <span class="info-label">شماره تراکنش:</span>
                            <span class="info-value">{{ $order->payment->transaction_id }}</span>
                        </div>
                        @endif
                        <div class="info-item">
                            <span class="info-label">تاریخ پرداخت:</span>
                            <span class="info-value">{{ $order->payment->created_at->format('Y/m/d H:i') }}</span>
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Terms and Conditions -->
                @if($settings && $settings->invoice_terms)
                <div class="terms-section">
                    <h3 class="section-title">شرایط و قوانین</h3>
                    <p style="color: var(--text-secondary); line-height: 1.8;">{{ $settings->invoice_terms }}</p>
                </div>
                @endif
                
                <!-- Footer -->
                <div class="footer">
                    <div style="margin-bottom: 1rem;">
                        <strong>{{ $settings->invoice_footer_text ?? 'با تشکر از خرید شما' }}</strong>
                    </div>
                    <div style="margin-bottom: 1rem;">
                        {{ $settings->invoice_company_name ?? 'کارین شاپ' }}
                    </div>
                    <div style="font-size: 0.8rem; opacity: 0.8; margin-bottom: 1rem;">
                        این فاکتور به صورت الکترونیکی تولید شده و نیازی به مهر و امضا ندارد.
                    </div>
                    <div style="font-size: 0.8rem; opacity: 0.7;">
                        تاریخ تولید: {{ now()->format('Y/m/d H:i:s') }}
                    </div>
                    @if($settings && ($settings->invoice_company_website || $settings->invoice_company_email || $settings->invoice_company_phone))
                    <div class="footer-links">
                        @if($settings->invoice_company_website)
                            <a href="{{ $settings->invoice_company_website }}" class="footer-link">🌐 وب‌سایت</a>
                        @endif
                        @if($settings->invoice_company_email)
                            <a href="mailto:{{ $settings->invoice_company_email }}" class="footer-link">📧 ایمیل</a>
                        @endif
                        @if($settings->invoice_company_phone)
                            <span class="footer-link">📞 {{ $settings->invoice_company_phone }}</span>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function downloadPDF() {
            // Show loading state
            const downloadBtn = document.querySelector('button[onclick="downloadPDF()"]');
            const originalText = downloadBtn.innerHTML;
            downloadBtn.innerHTML = '<svg class="w-4 h-4 ml-2 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>در حال تولید...';
            downloadBtn.disabled = true;
            
            const element = document.getElementById('invoice-content');
            const opt = {
                margin: [10, 10, 10, 10],
                filename: 'فاکتور-سفارش-{{ $order->order_number ?? $order->id }}.pdf',
                image: { 
                    type: 'jpeg', 
                    quality: 0.95,
                    crossOrigin: 'anonymous'
                },
                html2canvas: { 
                    scale: 2,
                    useCORS: true,
                    letterRendering: true,
                    allowTaint: true,
                    backgroundColor: '#ffffff',
                    removeContainer: true,
                    imageTimeout: 15000,
                    logging: false
                },
                jsPDF: { 
                    unit: 'mm', 
                    format: 'a5', 
                    orientation: 'portrait',
                    compress: true,
                    precision: 16
                },
                pagebreak: { 
                    mode: ['avoid-all', 'css', 'legacy'],
                    before: '.page-break-before',
                    after: '.page-break-after',
                    avoid: '.no-page-break'
                }
            };
            
            html2pdf().set(opt).from(element).save().then(function() {
                // Restore button state
                downloadBtn.innerHTML = originalText;
                downloadBtn.disabled = false;
            }).catch(function(error) {
                console.error('PDF generation failed:', error);
                downloadBtn.innerHTML = originalText;
                downloadBtn.disabled = false;
                alert('خطا در تولید PDF. لطفاً دوباره تلاش کنید.');
            });
        }
        
        // Enhanced print function
        function printInvoice() {
            // Hide action buttons before printing
            const actionButtons = document.querySelector('.no-print');
            if (actionButtons) {
                actionButtons.style.display = 'none';
            }
            
            // Trigger print
            window.print();
            
            // Show action buttons after printing
            setTimeout(() => {
                if (actionButtons) {
                    actionButtons.style.display = 'flex';
                }
            }, 1000);
        }
        
        // Update print button to use enhanced function
        document.addEventListener('DOMContentLoaded', function() {
            const printBtn = document.querySelector('button[onclick="window.print()"]');
            if (printBtn) {
                printBtn.setAttribute('onclick', 'printInvoice()');
            }
            
            // Add keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Ctrl+P for print
                if (e.ctrlKey && e.key === 'p') {
                    e.preventDefault();
                    printInvoice();
                }
                // Ctrl+S for download PDF
                if (e.ctrlKey && e.key === 's') {
                    e.preventDefault();
                    downloadPDF();
                }
            });
            
            // Add smooth scroll behavior
            document.documentElement.style.scrollBehavior = 'smooth';
            
            // Add loading animation for images
            const images = document.querySelectorAll('img');
            images.forEach(img => {
                img.addEventListener('load', function() {
                    this.style.opacity = '1';
                });
                img.style.opacity = '0';
                img.style.transition = 'opacity 0.3s ease';
            });
        });
        
        // Add responsive table handling
        function handleResponsiveTables() {
            const tables = document.querySelectorAll('.items-table');
            tables.forEach(table => {
                if (window.innerWidth < 768) {
                    table.style.fontSize = '0.8rem';
                } else {
                    table.style.fontSize = '';
                }
            });
        }
        
        window.addEventListener('resize', handleResponsiveTables);
        document.addEventListener('DOMContentLoaded', handleResponsiveTables);
    </script>
</body>
</html>