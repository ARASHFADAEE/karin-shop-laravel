<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    protected $queryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteCategory($categoryId)
    {
        $category = Category::find($categoryId);
        if ($category) {
            // بررسی اینکه آیا دسته‌بندی دارای زیردسته یا محصول است
            if ($category->children()->count() > 0) {
                session()->flash('error', 'این دسته‌بندی دارای زیردسته است و قابل حذف نیست.');
                return;
            }
            
            if ($category->products()->count() > 0) {
                session()->flash('error', 'این دسته‌بندی دارای محصول است و قابل حذف نیست.');
                return;
            }
            
            $category->delete();
            session()->flash('success', 'دسته‌بندی با موفقیت حذف شد.');
        }
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        $categories = Category::with(['parent', 'children'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('slug', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.admin.categories.index', compact('categories'));
    }
}
