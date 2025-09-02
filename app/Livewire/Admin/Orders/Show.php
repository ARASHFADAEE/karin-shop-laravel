<?php

namespace App\Livewire\Admin\Orders;

use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class Show extends Component
{
    public Order $order;

    public function mount(Order $order)
    {
        $this->order = $order->load(['user', 'orderItems.product', 'payment']);
    }

    public function updateStatus($newStatus)
    {
        $this->order->update(['status' => $newStatus]);
        $this->order->refresh();
        session()->flash('success', 'وضعیت سفارش به‌روزرسانی شد.');
    }

    public function calculateTotal()
    {
        return $this->order->orderItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });
    }

    public function printOrder()
    {
        try {
            $pdf = Pdf::loadView('pdf.invoice', ['order' => $this->order])
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'defaultFont' => 'IRANYekan',
                    'isRemoteEnabled' => true,
                    'isHtml5ParserEnabled' => true,
                    'fontSubsetting' => false,
                    'isPhpEnabled' => true,
                    'chroot' => public_path(),
                    'fontDir' => storage_path('fonts'),
                    'fontCache' => storage_path('fonts'),
                ]);
            
            return response()->streamDownload(
                fn () => print($pdf->output()),
                "invoice-order-{$this->order->id}.pdf",
                ['Content-Type' => 'application/pdf']
            );
        } catch (\Exception $e) {
            session()->flash('error', 'خطا در تولید فاکتور: ' . $e->getMessage());
        }
    }
    
    public function printShippingLabel()
    {
        try {
            $pdf = Pdf::loadView('pdf.shipping-label', ['order' => $this->order])
                ->setPaper([0, 0, 283.46, 425.20], 'portrait') // 100mm x 150mm
                ->setOptions([
                    'defaultFont' => 'IRANYekan',
                    'isRemoteEnabled' => true,
                    'isHtml5ParserEnabled' => true,
                    'fontSubsetting' => false,
                    'isPhpEnabled' => true,
                    'chroot' => public_path(),
                    'fontDir' => storage_path('fonts'),
                    'fontCache' => storage_path('fonts'),
                ]);
            
            return response()->streamDownload(
                fn () => print($pdf->output()),
                "shipping-label-order-{$this->order->id}.pdf",
                ['Content-Type' => 'application/pdf']
            );
        } catch (\Exception $e) {
            session()->flash('error', 'خطا در تولید برچسب ارسال: ' . $e->getMessage());
        }
    }
    
    public function downloadInvoice()
    {
        return $this->printOrder();
    }
    
    public function downloadShippingLabel()
    {
        return $this->printShippingLabel();
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.admin.orders.show');
    }
}
