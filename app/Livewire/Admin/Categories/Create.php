<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Create extends Component
{
    #[Rule('required|string|max:100')]
    public string $name = '';

    #[Rule('nullable|exists:categories,id')]
    public string $parent_id = '';

    #[Rule('nullable|string')]
    public string $description = '';

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
        while (Category::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        Category::create([
            'name' => $this->name,
            'parent_id' => $this->parent_id ?: null,
            'slug' => $slug,
            'description' => $this->description,
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
