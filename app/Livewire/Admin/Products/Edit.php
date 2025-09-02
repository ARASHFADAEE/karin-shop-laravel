<?php

namespace App\Livewire\Admin\Products;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Edit extends Component
{
    public Product $product;

    #[Rule('required|string|max:200')]
    public string $name = '';

    #[Rule('required|exists:categories,id')]
    public string $category_id = '';

    #[Rule('nullable|string')]
    public string $description = '';

    #[Rule('required|numeric|min:0')]
    public string $price = '';

    #[Rule('required|integer|min:0')]
    public string $stock = '';

    #[Rule('required|string|max:100')]
    public string $sku = '';

    #[Rule('required|in:active,draft,out_of_stock')]
    public string $status = 'active';

    public function mount(Product $product)
    {
        $this->product = $product;
        $this->name = $product->name;
        $this->category_id = $product->category_id;
        $this->description = $product->description ?? '';
        $this->price = $product->price;
        $this->stock = $product->stock;
        $this->sku = $product->sku;
        $this->status = $product->status;
    }

    public function generateSku()
    {
        $this->sku = 'PRD-' . strtoupper(Str::random(8));
    }

    public function generateSlug()
    {
        if ($this->name) {
            return Str::slug($this->name);
        }
        return '';
    }

    public function save()
    {
        // اعتبارسنجی با در نظر گیری محصول فعلی برای SKU
        $this->validate([
            'name' => 'required|string|max:200',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'sku' => 'required|string|max:100|unique:products,sku,' . $this->product->id,
            'status' => 'required|in:active,draft,out_of_stock',
        ]);

        $slug = $this->generateSlug();
        
        // بررسی یکتا بودن slug (به جز محصول فعلی)
        $originalSlug = $slug;
        $counter = 1;
        while (Product::where('slug', $slug)->where('id', '!=', $this->product->id)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $this->product->update([
            'name' => $this->name,
            'category_id' => $this->category_id,
            'slug' => $slug,
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock,
            'sku' => $this->sku,
            'status' => $this->status,
        ]);

        session()->flash('success', 'محصول با موفقیت به‌روزرسانی شد.');
        
        return $this->redirect(route('admin.products.index'), navigate: true);
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        $categories = Category::all();
        return view('livewire.admin.products.edit', compact('categories'));
    }
}
