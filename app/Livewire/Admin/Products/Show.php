<?php

namespace App\Livewire\Admin\Products;

use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Show extends Component
{
    public Product $product;

    public function mount(Product $product)
    {
        $this->product = $product->load(['category', 'images', 'featuredImages', 'attributes', 'reviews.user']);
    }

    public function toggleStatus()
    {
        $newStatus = match($this->product->status) {
            'active' => 'draft',
            'draft' => 'active',
            'out_of_stock' => 'active',
            default => 'active'
        };
        
        $this->product->update(['status' => $newStatus]);
        session()->flash('success', 'وضعیت محصول به‌روزرسانی شد.');
    }

    public function updateStock($newStock)
    {
        $this->product->update(['stock' => $newStock]);
        session()->flash('success', 'موجودی محصول به‌روزرسانی شد.');
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.admin.products.show');
    }
}
