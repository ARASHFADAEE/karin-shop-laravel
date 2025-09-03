<?php

namespace App\Livewire\Admin\Products;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductAttribute;
use App\Models\ProductImage;
use App\Models\ProductFeaturedImage;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

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

    #[Rule('required|string|unique:products,sku|max:100')]
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

    // Images
    public array $images = [];
    public array $featuredImages = [];

    // Product Attributes
    public array $productAttributes = [
        ['name' => '', 'value' => '']
    ];

    // Product Variants
    public array $productVariants = [];
    public bool $hasVariants = false;
    public string $variantType = 'simple'; // simple, variable
    
    // Filter Values
    public array $filterValues = [];
    public string $selectedBrand = '';
    public array $selectedColors = [];
    public array $selectedSizes = [];
    public array $selectedMaterials = [];
    
    // UI State
    public string $activeTab = 'basic'; // basic, variants, filters, seo, images
    public bool $isGeneratingSlug = false;
    public bool $showVariantForm = false;
    public int $editingVariantIndex = -1;

    public function mount()
    {
        $this->generateSku();
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

            // Ensure unique slug
            $originalSlug = $this->slug;
            $counter = 1;
            while (Product::where('slug', $this->slug)->exists()) {
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

    public function removeImage($index)
    {
        unset($this->images[$index]);
        $this->images = array_values($this->images);
    }

    public function removeFeaturedImage($index)
    {
        unset($this->featuredImages[$index]);
        $this->featuredImages = array_values($this->featuredImages);
    }

    public function updatedImages()
    {
        $this->validate([
            'images.*' => 'image|max:2048',
        ]);
    }

    public function updatedFeaturedImages()
    {
        $this->validate([
            'featuredImages.*' => 'image|max:2048',
        ]);
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
            
            // Ensure unique slug
            $originalSlug = $this->slug;
            $counter = 1;
            while (Product::where('slug', $this->slug)->exists()) {
                $this->slug = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        // Create product
        $product = Product::create([
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

        // Attach categories
        $categoryData = [];
        foreach ($this->selectedCategories as $categoryId) {
            $categoryData[$categoryId] = ['is_primary' => $categoryId == $this->primaryCategory];
        }
        $product->categories()->attach($categoryData);

        // Save attributes
        foreach ($this->productAttributes as $attribute) {
            if (!empty($attribute['name']) && !empty($attribute['value'])) {
                ProductAttribute::create([
                    'product_id' => $product->id,
                    'attribute_name' => $attribute['name'],
                    'attribute_value' => $attribute['value'],
                ]);
            }
        }

        // Upload images
        foreach ($this->images as $image) {
            $path = $image->store('products', 'public');
            ProductImage::create([
                'product_id' => $product->id,
                'image_url' => Storage::url($path),
            ]);
        }

        // Upload featured images
        foreach ($this->featuredImages as $image) {
            $path = $image->store('products/featured', 'public');
            ProductFeaturedImage::create([
                'product_id' => $product->id,
                'image_url' => Storage::url($path),
            ]);
        }

        // Save variants if product has variants
        if ($this->hasVariants && !empty($this->productVariants)) {
            foreach ($this->productVariants as $index => $variant) {
                if (!empty($variant['color']) || !empty($variant['size']) || !empty($variant['name'])) {
                    $variantData = [
                        'product_id' => $product->id,
                        'name' => $variant['name'] ?? '',
                        'sku' => $variant['sku'] ?? $this->generateVariantSku($product->sku, $variant),
                        'price' => $variant['price'] ?? $this->price,
                        'original_price' => $variant['original_price'] ?? $this->original_price,
                        'stock' => $variant['stock'] ?? 0,
                        'color' => $variant['color'] ?? null,
                        'size' => $variant['size'] ?? null,
                        'material' => $variant['material'] ?? null,
                        'brand' => $this->selectedBrand ?: null,
                        'status' => 'active',
                        'is_default' => $index === 0,
                        'sort_order' => $index
                    ];
                    
                    ProductVariant::create($variantData);
                }
            }
        } else {
            // Create default variant for simple products
            ProductVariant::create([
                'product_id' => $product->id,
                'name' => 'پیش‌فرض',
                'sku' => $product->sku,
                'price' => $this->price,
                'original_price' => $this->original_price,
                'stock' => $this->stock,
                'brand' => $this->selectedBrand ?: null,
                'status' => 'active',
                'is_default' => true,
                'sort_order' => 0
            ]);
        }

        session()->flash('success', 'محصول با موفقیت ایجاد شد.');
        
        return $this->redirect(route('admin.products.index'), navigate: true);
    }

    // Variant Management Methods
    public function addVariant()
    {
        $this->productVariants[] = [
            'name' => '',
            'sku' => '',
            'price' => $this->price,
            'original_price' => $this->original_price,
            'stock' => 0,
            'color' => '',
            'size' => '',
            'material' => '',
            'weight' => '',
            'status' => 'active'
        ];
    }

    public function removeVariant($index)
    {
        unset($this->productVariants[$index]);
        $this->productVariants = array_values($this->productVariants);
    }

    public function generateVariantSku($baseSku, $variant)
    {
        $suffix = '';
        if (!empty($variant['color'])) {
            $suffix .= strtoupper(substr($variant['color'], 0, 3));
        }
        if (!empty($variant['size'])) {
            $suffix .= strtoupper(substr($variant['size'], 0, 3));
        }
        
        return $baseSku . ($suffix ? '-' . $suffix : '-VAR' . (count($this->productVariants) + 1));
    }

    public function generateVariantsFromCombinations()
    {
        if (empty($this->selectedColors) && empty($this->selectedSizes)) {
            return;
        }

        $this->productVariants = [];
        $colors = $this->selectedColors ?: [''];
        $sizes = $this->selectedSizes ?: [''];

        foreach ($colors as $color) {
            foreach ($sizes as $size) {
                $name = trim(implode(' - ', array_filter([$color, $size])));
                $this->productVariants[] = [
                    'name' => $name ?: 'پیش‌فرض',
                    'sku' => $this->generateVariantSku($this->sku, ['color' => $color, 'size' => $size]),
                    'price' => $this->price,
                    'original_price' => $this->original_price,
                    'stock' => 0,
                    'color' => $color,
                    'size' => $size,
                    'material' => '',
                    'weight' => '',
                    'status' => 'active'
                ];
            }
        }
    }

    public function updatedHasVariants()
    {
        if ($this->hasVariants) {
            $this->variantType = 'variable';
            if (empty($this->productVariants)) {
                $this->addVariant();
            }
        } else {
            $this->variantType = 'simple';
            $this->productVariants = [];
        }
    }

    public function updatedSelectedColors()
    {
        if ($this->hasVariants) {
            $this->generateVariantsFromCombinations();
        }
    }

    public function updatedSelectedSizes()
    {
        if ($this->hasVariants) {
            $this->generateVariantsFromCombinations();
        }
    }

    // Tab Management
    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    // Category Events
    public function categoriesUpdated($data)
    {
        $this->selectedCategories = $data['selectedCategories'];
        $this->primaryCategory = $data['primaryCategory'];
    }





    #[Layout('layouts.admin')]
    public function render()
    {
        $categories = Category::all();
        $filters = \App\Models\ProductFilter::where('is_active', true)->orderBy('sort_order')->get();
        
        return view('livewire.admin.products.create', compact('categories', 'filters'));
    }
}
