<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>فاکتور سفارش {{ $order->order_number ?? '#' . $order->id }}</title>
    <style>
        @font-face {
             font-family: 'IRANYekan';
             src: url('/build/fonts/IRANYekanXVF.woff2') format('woff2'),
                  url('/build/fonts/IRANYekanXVF.woff') format('woff');
             font-weight: 100 900;
             font-style: normal;
             font-display: swap;
             font-variation-settings: "wght" 400, "dots" 2;
             unicode-range: U+0600-06FF, U+200C-200D, U+2010-2011, U+204F, U+2E41, U+FB50-FDFF, U+FE80-FEFC;
         }
    </style>
    <style>
        @page {
            margin: 15mm;
            size: A4;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'IRANYekan', 'Tahoma', 'Arial Unicode MS', 'DejaVu Sans', sans-serif;
            font-size: 13px;
            line-height: 1.8;
            margin: 0;
            padding: 0;
            direction: rtl;
            text-align: right;
        }
        
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 15px;
        }
        
        .logo {
            font-size: 22px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 8px;
        }
        
        .company-info {
            font-size: 10px;
            color: #666;
            line-height: 1.5;
        }
        
        .invoice-title {
            font-family: 'IRANYekan', 'Tahoma', 'Arial Unicode MS', 'DejaVu Sans', sans-serif;
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            margin: 15px 0;
            text-align: center;
        }
        
        .info-section {
            width: 100%;
            margin-bottom: 25px;
            overflow: hidden;
        }
        
        .info-box {
            width: 48%;
            float: right;
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            margin-left: 2%;
        }
        
        .info-box:first-child {
            margin-left: 0;
        }
        
        .info-box h3 {
            font-family: 'IRANYekan', 'Tahoma', 'Arial Unicode MS', 'DejaVu Sans', sans-serif;
            font-size: 13px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 8px;
            border-bottom: 1px solid #d1d5db;
            padding-bottom: 4px;
        }
        
        .info-item {
            font-family: 'IRANYekan', 'Tahoma', 'Arial Unicode MS', 'DejaVu Sans', sans-serif;
            margin-bottom: 6px;
            line-height: 1.6;
        }
        
        .info-label {
            font-family: 'IRANYekan', 'Tahoma', 'Arial Unicode MS', 'DejaVu Sans', sans-serif;
            font-weight: bold;
            color: #6b7280;
            display: inline-block;
            width: 70px;
        }
        
        .info-value {
            font-family: 'IRANYekan', 'Tahoma', 'Arial Unicode MS', 'DejaVu Sans', sans-serif;
            color: #1f2937;
        }
        
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-processing { background: #dbeafe; color: #1e40af; }
        .status-shipped { background: #d1fae5; color: #065f46; }
        .status-delivered { background: #dcfce7; color: #166534; }
        .status-cancelled { background: #fee2e2; color: #dc2626; }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            border: 1px solid #e5e7eb;
        }
        
        .items-table th {
            font-family: 'IRANYekan', 'Tahoma', 'Arial Unicode MS', 'DejaVu Sans', sans-serif;
            background: #374151;
            color: #fff;
            padding: 8px;
            text-align: right;
            font-weight: bold;
            font-size: 11px;
            border-bottom: 1px solid #4b5563;
        }
        
        .items-table td {
            font-family: 'IRANYekan', 'Tahoma', 'Arial Unicode MS', 'DejaVu Sans', sans-serif;
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
            text-align: right;
            vertical-align: top;
        }
        
        .items-table tr:last-child td {
            border-bottom: none;
        }
        
        .items-table tr:nth-child(even) {
            background: #f9fafb;
        }
        
        .product-name {
            font-family: 'IRANYekan', 'Tahoma', 'Arial Unicode MS', 'DejaVu Sans', sans-serif;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 2px;
        }
        
        .product-sku {
            font-family: 'IRANYekan', 'Tahoma', 'Arial Unicode MS', 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #6b7280;
        }
        
        .total-section {
            background: #f8fafc;
            padding: 15px;
            border: 1px solid #e5e7eb;
            margin-top: 15px;
        }
        
        .total-row {
            margin-bottom: 8px;
            font-size: 13px;
            overflow: hidden;
        }
        
        .total-label {
            font-family: 'IRANYekan', 'Tahoma', 'Arial Unicode MS', 'DejaVu Sans', sans-serif;
            float: right;
            width: 70%;
        }
        
        .total-value {
            font-family: 'IRANYekan', 'Tahoma', 'Arial Unicode MS', 'DejaVu Sans', sans-serif;
            float: left;
            width: 30%;
            text-align: left;
            font-weight: bold;
        }
        
        .total-row.final {
            font-family: 'IRANYekan', 'Tahoma', 'Arial Unicode MS', 'DejaVu Sans', sans-serif;
            border-top: 2px solid #374151;
            margin-top: 10px;
            padding-top: 10px;
            font-size: 15px;
            font-weight: bold;
            color: #1f2937;
        }
        
        .footer {
            font-family: 'IRANYekan', 'Tahoma', 'Arial Unicode MS', 'DejaVu Sans', sans-serif;
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
            line-height: 1.6;
        }
        
        .payment-info {
            background: #ecfdf5;
            border: 1px solid #10b981;
            padding: 12px;
            margin: 15px 0;
        }
        
        .payment-info h4 {
            font-family: 'IRANYekan', 'Tahoma', 'Arial Unicode MS', 'DejaVu Sans', sans-serif;
            color: #065f46;
            margin-bottom: 8px;
            font-weight: bold;
            font-size: 12px;
        }
        
        .address-box {
            width: 100%;
            background: #f8fafc;
            padding: 12px;
            border: 1px solid #e5e7eb;
            margin-bottom: 15px;
        }
        
        .address-box h3 {
            font-family: 'IRANYekan', 'Tahoma', 'Arial Unicode MS', 'DejaVu Sans', sans-serif;
            font-size: 13px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 8px;
            border-bottom: 1px solid #d1d5db;
            padding-bottom: 4px;
        }
        
        .address-text {
            font-family: 'IRANYekan', 'Tahoma', 'Arial Unicode MS', 'DejaVu Sans', sans-serif;
            line-height: 1.8;
            color: #1f2937;
        }
        
        .number {
            direction: ltr;
            unicode-bidi: embed;
            display: inline-block;
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
        
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>
<body>
    <div class="watermark">کارین شاپ</div>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo">کارین شاپ</div>
            <div class="company-info">
                فروشگاه آنلاین کارین شاپ<br>
                تلفن: ۰۲۱-۱۲۳۴۵۶۷۸ | ایمیل: info@karinshop.com
            </div>
        </div>
        
        <!-- Invoice Title -->
        <h1 class="invoice-title">فاکتور فروش</h1>
        
        <!-- Order & Customer Info -->
        <div class="info-section">
            <div class="info-box">
                <h3>اطلاعات سفارش</h3>
                <div class="info-item">
                    <span class="info-label">شماره:</span>
                    <span class="info-value">{{ $order->order_number ?? '#' . $order->id }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">تاریخ:</span>
                    <span class="info-value">{{ $order->created_at->format('Y/m/d H:i') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">وضعیت:</span>
                    <span class="status-badge status-{{ $order->status }}">
                        @switch($order->status)
                            @case('pending') در انتظار @break
                            @case('processing') در حال پردازش @break
                            @case('shipped') ارسال شده @break
                            @case('delivered') تحویل داده شده @break
                            @case('cancelled') لغو شده @break
                            @default {{ $order->status }}
                        @endswitch
                    </span>
                </div>
            </div>
            
            <div class="info-box">
                <h3>اطلاعات مشتری</h3>
                <div class="info-item">
                    <span class="info-label">نام:</span>
                    <span class="info-value">{{ $order->user->name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">ایمیل:</span>
                    <span class="info-value">{{ $order->user->email }}</span>
                </div>
                @if($order->user->phone)
                <div class="info-item">
                    <span class="info-label">تلفن:</span>
                    <span class="info-value">{{ $order->user->phone }}</span>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Shipping Address -->
        @if($order->shipping_address)
        <div class="address-box">
            <h3>آدرس ارسال</h3>
            <div class="address-text">{{ $order->shipping_address }}</div>
        </div>
        @endif
        
        <!-- Order Items -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50%">محصول</th>
                    <th style="width: 15%">قیمت واحد</th>
                    <th style="width: 10%">تعداد</th>
                    <th style="width: 25%">مجموع</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->orderItems as $item)
                <tr>
                    <td>
                        <div class="product-name">{{ $item->product->name }}</div>
                        <div class="product-sku">کد محصول: {{ $item->product->sku }}</div>
                    </td>
                    <td>{{ number_format($item->price) }} تومان</td>
                    <td>{{ $item->quantity }}</td>
                    <td><strong>{{ number_format($item->price * $item->quantity) }} تومان</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Total Section -->
        <div class="total-section">
            <div class="total-row">
                <span class="total-label">تعداد کل آیتم‌ها:</span>
                <span class="total-value">{{ $order->orderItems->count() }} قلم</span>
            </div>
            <div class="total-row">
                <span class="total-label">تعداد کل محصولات:</span>
                <span class="total-value">{{ $order->orderItems->sum('quantity') }} عدد</span>
            </div>
            <div class="total-row final">
                <span class="total-label">مبلغ کل:</span>
                <span class="total-value">{{ number_format($order->total_amount) }} تومان</span>
            </div>
        </div>
        
        <!-- Payment Info -->
        @if($order->payment)
        <div class="payment-info">
            <h4>اطلاعات پرداخت</h4>
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
        @endif
        
        <!-- Footer -->
        <div class="footer">
            <p>با تشکر از خرید شما | کارین شاپ</p>
            <p>این فاکتور به صورت الکترونیکی تولید شده و نیازی به مهر و امضا ندارد.</p>
            <p>تاریخ تولید: {{ now()->format('Y/m/d H:i:s') }}</p>
        </div>
    </div>
</body>
</html>