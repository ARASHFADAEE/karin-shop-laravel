<?php

namespace App\Livewire\Admin\Products;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductAttribute;
use App\Models\ProductImage;
use App\Models\ProductFeaturedImage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;
    public Product $product;

    // Basic Product Fields
    #[Rule('required|string|max:200')]
    public string $name = '';

    #[Rule('nullable|string|max:200')]
    public string $slug = '';

    #[Rule('nullable|string')]
    public string $description = '';

    #[Rule('required|numeric|min:0')]
    public string $price = '';

    #[Rule('nullable|numeric|min:0')]
    public string $original_price = '';

    #[Rule('nullable|numeric|min:0|max:100')]
    public string $discount_percentage = '';

    #[Rule('nullable|numeric|min:0')]
    public string $discount_amount = '';

    #[Rule('boolean')]
    public bool $has_discount = false;

    #[Rule('nullable|date')]
    public string $discount_starts_at = '';

    #[Rule('nullable|date')]
    public string $discount_ends_at = '';

    #[Rule('required|integer|min:0')]
    public string $stock = '';

    #[Rule('required|string|max:100')]
    public string $sku = '';

    #[Rule('required|in:active,draft,out_of_stock')]
    public string $status = 'active';

    // SEO Fields
    #[Rule('nullable|string|max:255')]
    public string $meta_title = '';

    #[Rule('nullable|string')]
    public string $meta_description = '';

    #[Rule('nullable|string')]
    public string $meta_keywords = '';

    #[Rule('nullable|string|max:255')]
    public string $og_title = '';

    #[Rule('nullable|string')]
    public string $og_description = '';

    #[Rule('nullable|string|max:500')]
    public string $og_image = '';

    // Categories (Multi-select)
    public array $selectedCategories = [];
    public string $primaryCategory = '';
    public string $categorySearch = '';

    // Product Attributes
    public array $productAttributes = [];
    
    // Image Upload
    public $images = [];
    public $featuredImages = [];

    public bool $isGeneratingSlug = false;

    public function mount(Product $product)
    {
        $this->product = $product->load(['images', 'featuredImages', 'categories', 'attributes']);
        
        // Load basic product data
        $this->name = $product->name;
        $this->slug = $product->slug;
        $this->description = $product->description ?? '';
        $this->price = $product->price;
        $this->original_price = $product->original_price ?? '';
        $this->discount_percentage = $product->discount_percentage ?? '';
        $this->discount_amount = $product->discount_amount ?? '';
        $this->has_discount = $product->has_discount;
        $this->discount_starts_at = $product->discount_starts_at ? $product->discount_starts_at->format('Y-m-d\TH:i') : '';
        $this->discount_ends_at = $product->discount_ends_at ? $product->discount_ends_at->format('Y-m-d\TH:i') : '';
        $this->stock = $product->stock;
        $this->sku = $product->sku;
        $this->status = $product->status;
        
        // SEO fields
        $this->meta_title = $product->meta_title ?? '';
        $this->meta_description = $product->meta_description ?? '';
        $this->meta_keywords = $product->meta_keywords ?? '';
        $this->og_title = $product->og_title ?? '';
        $this->og_description = $product->og_description ?? '';
        $this->og_image = $product->og_image ?? '';
        
        // Categories
        $this->selectedCategories = $product->categories->pluck('id')->toArray();
        $primaryCategory = $product->categories->where('pivot.is_primary', true)->first();
        $this->primaryCategory = $primaryCategory ? $primaryCategory->id : '';
        
        // Attributes
        $this->productAttributes = $product->attributes->map(function($attr) {
            return ['name' => $attr->name, 'value' => $attr->value];
        })->toArray();
        
        if (empty($this->productAttributes)) {
            $this->productAttributes = [['name' => '', 'value' => '']];
        }
    }

    public function generateSku()
    {
        $this->sku = 'PRD-' . strtoupper(Str::random(8));
    }

    public function generateSlug()
    {
        if (empty($this->name)) {
            session()->flash('error', 'ابتدا نام محصول را وارد کنید.');
            return;
        }

        $this->isGeneratingSlug = true;

        try {
            // Call translation API
            $response = Http::withHeaders([
                'one-api-token' => '645888:669bf7ffa1c57',
                'Content-Type' => 'application/json',
            ])->post('https://api.one-api.ir/translate/v1/google/', [
                'source' => 'fa',
                'target' => 'en',
                'text' => $this->name
            ]);

            if ($response->successful() && $response->json('status') === 200) {
                $translatedText = $response->json('result');
                $this->slug = Str::slug($translatedText);
            } else {
                // Fallback to Persian slug
                $this->slug = Str::slug($this->name);
            }

            // Ensure unique slug (except current product)
            $originalSlug = $this->slug;
            $counter = 1;
            while (Product::where('slug', $this->slug)->where('id', '!=', $this->product->id)->exists()) {
                $this->slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            session()->flash('success', 'اسلاگ با موفقیت تولید شد.');
        } catch (\Exception $e) {
            // Fallback to Persian slug
            $this->slug = Str::slug($this->name);
            session()->flash('error', 'خطا در تولید اسلاگ. از نام فارسی استفاده شد.');
        } finally {
            $this->isGeneratingSlug = false;
        }
    }

    public function addAttribute()
    {
        $this->productAttributes[] = ['name' => '', 'value' => ''];
    }

    public function removeAttribute($index)
    {
        if (count($this->productAttributes) > 1) {
            unset($this->productAttributes[$index]);
            $this->productAttributes = array_values($this->productAttributes);
        }
    }

    public function removeCategory($categoryId)
    {
        $this->selectedCategories = array_filter($this->selectedCategories, function($id) use ($categoryId) {
            return $id != $categoryId;
        });
        
        // Reset primary category if it was the removed one
        if ($this->primaryCategory == $categoryId) {
            $this->primaryCategory = '';
        }
        
        // Re-index array
        $this->selectedCategories = array_values($this->selectedCategories);
    }

    public function save()
    {
        $this->validate();

        // Validate categories
        if (empty($this->selectedCategories)) {
            session()->flash('error', 'حداقل یک دسته‌بندی انتخاب کنید.');
            return;
        }

        if (empty($this->primaryCategory)) {
            $this->primaryCategory = $this->selectedCategories[0];
        }

        // Generate slug if empty
        if (empty($this->slug)) {
            $this->slug = Str::slug($this->name);
            
            // Ensure unique slug (except current product)
            $originalSlug = $this->slug;
            $counter = 1;
            while (Product::where('slug', $this->slug)->where('id', '!=', $this->product->id)->exists()) {
                $this->slug = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        // Update product
        $this->product->update([
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'og_title' => $this->og_title,
            'og_description' => $this->og_description,
            'og_image' => $this->og_image,
            'price' => $this->price,
            'original_price' => $this->original_price ?: null,
            'discount_percentage' => $this->discount_percentage ?: null,
            'discount_amount' => $this->discount_amount ?: null,
            'has_discount' => $this->has_discount,
            'discount_starts_at' => $this->discount_starts_at ?: null,
            'discount_ends_at' => $this->discount_ends_at ?: null,
            'stock' => $this->stock,
            'sku' => $this->sku,
            'status' => $this->status,
        ]);

        // Update categories
        $categoryData = [];
        foreach ($this->selectedCategories as $categoryId) {
            $categoryData[$categoryId] = ['is_primary' => $categoryId == $this->primaryCategory];
        }
        $this->product->categories()->sync($categoryData);

        // Update attributes
        $this->product->attributes()->delete();
        foreach ($this->productAttributes as $attribute) {
            if (!empty($attribute['name']) && !empty($attribute['value'])) {
                ProductAttribute::create([
                    'product_id' => $this->product->id,
                    'name' => $attribute['name'],
                    'value' => $attribute['value'],
                ]);
            }
        }

        session()->flash('success', 'محصول با موفقیت به‌روزرسانی شد.');
        
        return $this->redirect(route('admin.products.show', $this->product), navigate: true);
    }
    
    public function uploadImages()
    {
        $this->validate([
            'images.*' => 'nullable|image|max:2048',
            'featuredImages.*' => 'nullable|image|max:2048',
        ]);

        try {
            // Upload regular images
            foreach ($this->images as $image) {
                $path = $image->store('products', 'public');
                
                ProductImage::create([
                    'product_id' => $this->product->id,
                    'image_url' => Storage::url($path),
                ]);
            }

            // Upload featured images
            foreach ($this->featuredImages as $image) {
                $path = $image->store('products/featured', 'public');
                
                ProductFeaturedImage::create([
                    'product_id' => $this->product->id,
                    'image_url' => Storage::url($path),
                ]);
            }

            $this->product->refresh();
            $this->product->load(['images', 'featuredImages']);
            
            // Reset file inputs
            $this->images = [];
            $this->featuredImages = [];
            
            session()->flash('success', 'تصاویر با موفقیت آپلود شدند.');
        } catch (\Exception $e) {
            session()->flash('error', 'خطا در آپلود تصاویر: ' . $e->getMessage());
        }
    }
    
    public function deleteImage($imageId, $type = 'regular')
    {
        try {
            if ($type === 'featured') {
                $image = ProductFeaturedImage::find($imageId);
            } else {
                $image = ProductImage::find($imageId);
            }

            if ($image) {
                // Delete file from storage
                $imagePath = str_replace('/storage/', '', $image->image_url);
                Storage::disk('public')->delete($imagePath);
                
                // Delete from database
                $image->delete();
                
                $this->product->refresh();
                $this->product->load(['images', 'featuredImages']);
                
                session()->flash('success', 'تصویر با موفقیت حذف شد.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'خطا در حذف تصویر: ' . $e->getMessage());
        }
    }
    
    public function setAsFeatured($imageId)
    {
        try {
            $image = ProductImage::find($imageId);
            if ($image) {
                // Move to featured images
                ProductFeaturedImage::create([
                    'product_id' => $this->product->id,
                    'image_url' => $image->image_url,
                ]);
                
                // Delete from regular images
                $image->delete();
                
                $this->product->refresh();
                $this->product->load(['images', 'featuredImages']);
                
                session()->flash('success', 'تصویر به عنوان تصویر شاخص تنظیم شد.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'خطا در تنظیم تصویر شاخص: ' . $e->getMessage());
        }
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        $categories = Category::all();
        return view('livewire.admin.products.edit', compact('categories'));
    }
}
