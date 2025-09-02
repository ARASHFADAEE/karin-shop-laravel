<?php

namespace App\Livewire\Admin\Products;

use App\Models\Product;
use App\Models\Category;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $category_id = '';
    public $status = '';
    public $perPage = 10;

    protected $queryString = ['search', 'category_id', 'status'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoryId()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function deleteProduct($productId)
    {
        $product = Product::find($productId);
        if ($product) {
            // بررسی اینکه آیا محصول در سفارشات استفاده شده
            if ($product->orderItems()->count() > 0) {
                session()->flash('error', 'این محصول در سفارشات استفاده شده و قابل حذف نیست.');
                return;
            }
            
            // حذف تصاویر مرتبط
            $product->images()->delete();
            $product->featuredImages()->delete();
            $product->attributes()->delete();
            
            $product->delete();
            session()->flash('success', 'محصول با موفقیت حذف شد.');
        }
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        $products = Product::with('category')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('sku', 'like', '%' . $this->search . '%');
            })
            ->when($this->category_id, function ($query) {
                $query->where('category_id', $this->category_id);
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->latest()
            ->paginate($this->perPage);

        $categories = Category::all();

        return view('livewire.admin.products.index', compact('products', 'categories'));
    }
}
