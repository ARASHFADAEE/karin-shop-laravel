<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;

class OrderPrintController extends Controller
{
    public function invoiceHtml(Order $order)
    {
        $order->load(['user', 'orderItems.product', 'payment']);
        $settings = Setting::first();
        
        return view('admin.orders.invoice-html', compact('order', 'settings'));
    }
    
    public function shippingLabelHtml(Order $order)
    {
        $order->load(['user', 'orderItems.product', 'payment']);
        $settings = Setting::first();
        
        return view('admin.orders.shipping-label-html', compact('order', 'settings'));
    }
}