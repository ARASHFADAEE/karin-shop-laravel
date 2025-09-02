<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="utf-8">
    <title>برچسب ارسال سفارش {{ $order->order_number ?? '#' . $order->id }}</title>
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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'IRANYekan', 'Tahoma', 'DejaVu Sans', 'Arial Unicode MS', sans-serif;
            font-size: 11px;
            line-height: 1.6;
            color: #000;
            background: #fff;
            direction: rtl;
            text-align: right;
            unicode-bidi: bidi-override;
        }
        
        .label {
            width: 100mm;
            height: 150mm;
            border: 2px solid #000;
            padding: 8mm;
            margin: 0 auto;
            position: relative;
            background: #fff;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 5mm;
            margin-bottom: 5mm;
        }
        
        .logo {
            font-size: 18px;
            font-weight: 700;
            color: #000;
            margin-bottom: 2mm;
        }
        
        .company-info {
            font-size: 8px;
            color: #333;
        }
        
        .section {
            margin-bottom: 4mm;
            border: 1px solid #ccc;
            padding: 3mm;
            background: #f9f9f9;
        }
        
        .section-title {
            font-size: 10px;
            font-weight: 600;
            color: #000;
            margin-bottom: 2mm;
            text-transform: uppercase;
            border-bottom: 1px solid #666;
            padding-bottom: 1mm;
        }
        
        .order-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3mm;
        }
        
        .order-number {
            font-size: 14px;
            font-weight: 700;
            color: #000;
        }
        
        .order-date {
            font-size: 9px;
            color: #666;
        }
        
        .customer-name {
            font-size: 11px;
            font-weight: 600;
            color: #000;
            margin-bottom: 1mm;
        }
        
        .customer-phone {
            font-size: 10px;
            color: #333;
            margin-bottom: 1mm;
        }
        
        .address {
            font-size: 9px;
            line-height: 1.3;
            color: #000;
            min-height: 15mm;
            border: 1px dashed #999;
            padding: 2mm;
            background: #fff;
        }
        
        .items-summary {
            font-size: 9px;
            color: #333;
        }
        
        .items-count {
            font-weight: 600;
            color: #000;
        }
        
        .barcode-section {
            text-align: center;
            margin-top: 3mm;
            border-top: 1px solid #ccc;
            padding-top: 2mm;
        }
        
        .barcode {
            font-family: 'Courier New', monospace;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 2px;
            color: #000;
            margin-bottom: 1mm;
        }
        
        .barcode-text {
            font-size: 8px;
            color: #666;
        }
        
        .status-badge {
            position: absolute;
            top: 5mm;
            left: 5mm;
            background: #000;
            color: #fff;
            padding: 1mm 2mm;
            font-size: 8px;
            font-weight: 600;
            border-radius: 2mm;
        }
        
        .priority {
            position: absolute;
            top: 5mm;
            right: 5mm;
            background: #ff0000;
            color: #fff;
            padding: 1mm 2mm;
            font-size: 8px;
            font-weight: 600;
            border-radius: 2mm;
        }
        
        .footer {
            position: absolute;
            bottom: 3mm;
            left: 3mm;
            right: 3mm;
            text-align: center;
            font-size: 7px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 1mm;
        }
        
        .qr-placeholder {
            width: 15mm;
            height: 15mm;
            border: 1px solid #000;
            display: inline-block;
            text-align: center;
            line-height: 15mm;
            font-size: 6px;
            color: #666;
            margin-top: 2mm;
        }
        
        .handling-instructions {
            font-size: 8px;
            color: #000;
            font-weight: 500;
            text-align: center;
            margin: 2mm 0;
            padding: 1mm;
            border: 1px solid #000;
            background: #ffffcc;
        }
    </style>
</head>
<body>
    <div class="label">
        <!-- Status Badge -->
        <div class="status-badge">
            @switch($order->status)
                @case('pending') انتظار @break
                @case('processing') پردازش @break
                @case('shipped') ارسال @break
                @case('delivered') تحویل @break
                @case('cancelled') لغو @break
                @default {{ $order->status }}
            @endswitch
        </div>
        
        <!-- Priority Badge -->
        @if($order->created_at->diffInHours(now()) < 24)
        <div class="priority">فوری</div>
        @endif
        
        <!-- Header -->
        <div class="header">
            <div class="logo">کارین شاپ</div>
            <div class="company-info">فروشگاه آنلاین | ۰۲۱-۱۲۳۴۵۶۷۸</div>
        </div>
        
        <!-- Order Info -->
        <div class="order-info">
            <div class="order-number">سفارش {{ $order->order_number ?? '#' . $order->id }}</div>
            <div class="order-date">{{ $order->created_at->format('Y/m/d') }}</div>
        </div>
        
        <!-- Customer Section -->
        <div class="section">
            <div class="section-title">مشتری</div>
            <div class="customer-name">{{ $order->user->name }}</div>
            @if($order->user->phone)
            <div class="customer-phone">تلفن: {{ $order->user->phone }}</div>
            @endif
        </div>
        
        <!-- Shipping Address -->
        <div class="section">
            <div class="section-title">آدرس ارسال</div>
            <div class="address">
                @if($order->shipping_address)
                    {{ $order->shipping_address }}
                @else
                    آدرس ارسال مشخص نشده است
                @endif
            </div>
        </div>
        
        <!-- Items Summary -->
        <div class="section">
            <div class="section-title">محتویات بسته</div>
            <div class="items-summary">
                <span class="items-count">{{ $order->orderItems->count() }}</span> قلم محصول، 
                <span class="items-count">{{ $order->orderItems->sum('quantity') }}</span> عدد
            </div>
            <div style="font-size: 8px; margin-top: 1mm; color: #666;">
                ارزش: {{ number_format($order->total_amount) }} تومان
            </div>
        </div>
        
        <!-- Handling Instructions -->
        <div class="handling-instructions">
            ⚠️ با احتیاط حمل شود - محتویات شکستنی
        </div>
        
        <!-- Barcode Section -->
        <div class="barcode-section">
            <div class="barcode">*{{ str_pad($order->id, 8, '0', STR_PAD_LEFT) }}*</div>
            <div class="barcode-text">کد پیگیری: KS{{ $order->id }}{{ date('y') }}</div>
            
            <!-- QR Code Placeholder -->
            <div class="qr-placeholder">
                QR
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            تولید شده در {{ now()->format('Y/m/d H:i') }} | کارین شاپ
        </div>
    </div>
</body>
</html>