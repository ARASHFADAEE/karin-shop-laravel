<?php

namespace App\Livewire\Admin\Products;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductFeaturedImage;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

class ImageUpload extends Component
{
    use WithFileUploads;

    public Product $product;
    public $images = [];
    public $featuredImages = [];
    public $uploading = false;

    public function mount(Product $product)
    {
        $this->product = $product->load(['images', 'featuredImages']);
    }

    public function updatedImages()
    {
        $this->validate([
            'images.*' => 'image|max:2048', // 2MB Max
        ]);
    }

    public function updatedFeaturedImages()
    {
        $this->validate([
            'featuredImages.*' => 'image|max:2048', // 2MB Max
        ]);
    }

    public function uploadImages()
    {
        $this->uploading = true;
        
        try {
            // Upload regular images
            foreach ($this->images as $image) {
                $path = $image->store('products', 'public');
                
                ProductImage::create([
                    'product_id' => $this->product->id,
                    'image_path' => Storage::url($path),
                    'alt_text' => $this->product->name,
                ]);
            }

            // Upload featured images
            foreach ($this->featuredImages as $image) {
                $path = $image->store('products/featured', 'public');
                
                ProductFeaturedImage::create([
                    'product_id' => $this->product->id,
                    'image_path' => Storage::url($path),
                    'alt_text' => $this->product->name . ' - تصویر شاخص',
                ]);
            }

            $this->product->refresh();
            $this->product->load(['images', 'featuredImages']);
            
            $this->reset(['images', 'featuredImages']);
            session()->flash('success', 'تصاویر با موفقیت آپلود شدند.');
            
        } catch (\Exception $e) {
            session()->flash('error', 'خطا در آپلود تصاویر: ' . $e->getMessage());
        } finally {
            $this->uploading = false;
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
                $imagePath = str_replace('/storage/', '', $image->image_path);
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
                    'image_path' => $image->image_path,
                    'alt_text' => $image->alt_text . ' - تصویر شاخص',
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
        return view('livewire.admin.products.image-upload');
    }
}
