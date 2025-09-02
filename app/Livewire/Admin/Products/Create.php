<?php

namespace App\Livewire\Admin\Products;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Create extends Component
{
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

    #[Rule('required|string|unique:products,sku|max:100')]
    public string $sku = '';

    #[Rule('required|in:active,draft,out_of_stock')]
    public string $status = 'active';

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
        $this->validate();

        $slug = $this->generateSlug();
        
        // بررسی یکتا بودن slug
        $originalSlug = $slug;
        $counter = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        Product::create([
            'name' => $this->name,
            'category_id' => $this->category_id,
            'slug' => $slug,
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock,
            'sku' => $this->sku,
            'status' => $this->status,
        ]);

        session()->flash('success', 'محصول جدید با موفقیت ایجاد شد.');
        
        return $this->redirect(route('admin.products.index'), navigate: true);
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        $categories = Category::all();
        return view('livewire.admin.products.create', compact('categories'));
    }
}
