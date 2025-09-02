<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Edit extends Component
{
    public Category $category;

    #[Rule('required|string|max:100')]
    public string $name = '';

    #[Rule('nullable|exists:categories,id')]
    public string $parent_id = '';

    #[Rule('nullable|string')]
    public string $description = '';

    public function mount(Category $category)
    {
        $this->category = $category;
        $this->name = $category->name;
        $this->parent_id = $category->parent_id ?? '';
        $this->description = $category->description ?? '';
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
        
        // بررسی یکتا بودن slug (به جز دسته فعلی)
        $originalSlug = $slug;
        $counter = 1;
        while (Category::where('slug', $slug)->where('id', '!=', $this->category->id)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $this->category->update([
            'name' => $this->name,
            'parent_id' => $this->parent_id ?: null,
            'slug' => $slug,
            'description' => $this->description,
        ]);

        session()->flash('success', 'دسته‌بندی با موفقیت به‌روزرسانی شد.');
        
        return $this->redirect(route('admin.categories.index'), navigate: true);
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        $parentCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $this->category->id)
            ->get();
        return view('livewire.admin.categories.edit', compact('parentCategories'));
    }
}
