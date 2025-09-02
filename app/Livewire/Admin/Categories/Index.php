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
    public $parentFilter = '';
    public $statusFilter = '';
    public $perPage = 10;

    protected $queryString = ['search', 'parentFilter', 'statusFilter'];
    
    public function updatedParentFilter()
    {
        $this->resetPage();
    }
    
    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($categoryId)
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
    
    public function deleteCategory($categoryId)
    {
        return $this->delete($categoryId);
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        $categories = Category::with(['parent', 'children'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('slug', 'like', '%' . $this->search . '%');
            })
            ->when($this->parentFilter !== '', function ($query) {
                if ($this->parentFilter === 'root') {
                    $query->whereNull('parent_id');
                } else {
                    $query->where('parent_id', $this->parentFilter);
                }
            })
            ->when($this->statusFilter !== '', function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.admin.categories.index', compact('categories'));
    }
}
