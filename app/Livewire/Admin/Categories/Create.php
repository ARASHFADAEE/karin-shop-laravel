<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Create extends Component
{
    #[Rule('required|string|max:100|unique:categories,name')]
    public string $name = '';
    
    #[Rule('required|string|max:150|unique:categories,slug')]
    public string $slug = '';

    #[Rule('nullable|exists:categories,id')]
    public string $parent_id = '';

    #[Rule('nullable|string')]
    public string $description = '';
    
    #[Rule('required|in:active,inactive')]
    public string $status = 'active';

    public bool $isGeneratingSlug = false;

    public function generateSlug()
    {
        if (empty($this->name)) {
            session()->flash('error', 'ابتدا نام دسته‌بندی را وارد کنید.');
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
                $slug = Str::slug($translatedText);
            } else {
                // Fallback to Persian slug
                $slug = Str::slug($this->name);
            }

            // Ensure unique slug
            $originalSlug = $slug;
            $counter = 1;
            while (Category::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $this->slug = $slug;
            session()->flash('success', 'اسلاگ با موفقیت تولید شد: ' . $slug);
            return $slug;
        } catch (\Exception $e) {
            // Fallback to Persian slug
            $slug = Str::slug($this->name);
            $this->slug = $slug;
            session()->flash('error', 'خطا در تولید اسلاگ. از نام فارسی استفاده شد.');
            return $slug;
        } finally {
            $this->isGeneratingSlug = false;
        }
    }

    public function save()
    {
        // اگر slug خالی است، آن را تولید کن
        if (empty($this->slug)) {
            $this->generateSlug();
        }
        
        $this->validate();

        Category::create([
            'name' => $this->name,
            'parent_id' => $this->parent_id ?: null,
            'slug' => $this->slug,
            'description' => $this->description,
            'status' => $this->status,
        ]);

        session()->flash('success', 'دسته‌بندی جدید با موفقیت ایجاد شد.');
        
        return $this->redirect(route('admin.categories.index'), navigate: true);
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        $parentCategories = Category::whereNull('parent_id')->get();
        return view('livewire.admin.categories.create', compact('parentCategories'));
    }
}
