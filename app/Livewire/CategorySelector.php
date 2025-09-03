<?php

namespace App\Livewire;

use App\Models\Category;
use Livewire\Component;
use Livewire\Attributes\Modelable;

class CategorySelector extends Component
{
    public array $selectedCategories = [];
    public string $primaryCategory = '';
    
    public string $search = '';
    public bool $showSelected = false;
    public string $mode = 'checkbox'; // checkbox, radio, dropdown
    public bool $allowMultiple = true;
    public bool $showHierarchy = true;
    public int $maxHeight = 300;
    public string $placeholder = 'جستجو در دسته‌بندی‌ها...';
    
    // Computed properties
    public function getFilteredCategoriesProperty()
    {
        $categories = Category::orderBy('name')->get();
        
        if (empty($this->search)) {
            return $categories;
        }
        
        return $categories->filter(function($category) {
            return str_contains(strtolower($category->name), strtolower($this->search)) ||
                   str_contains(strtolower($category->slug ?? ''), strtolower($this->search));
        });
    }
    
    public function getSelectedCategoriesDataProperty()
    {
        if (empty($this->selectedCategories)) {
            return collect();
        }
        
        return Category::whereIn('id', $this->selectedCategories)->get();
    }
    
    public function getCategoryHierarchyProperty()
    {
        $categories = $this->filteredCategories;
        $hierarchy = [];
        
        // Group by parent
        $grouped = $categories->groupBy('parent_id');
        
        // Get root categories (parent_id = null)
        $roots = $grouped->get(null, collect());
        
        foreach ($roots as $root) {
            $hierarchy[] = [
                'category' => $root,
                'level' => 0,
                'children' => $this->buildHierarchy($grouped, $root->id, 1)
            ];
        }
        
        return $hierarchy;
    }
    
    private function buildHierarchy($grouped, $parentId, $level)
    {
        $children = [];
        $items = $grouped->get($parentId, collect());
        
        foreach ($items as $item) {
            $children[] = [
                'category' => $item,
                'level' => $level,
                'children' => $this->buildHierarchy($grouped, $item->id, $level + 1)
            ];
        }
        
        return $children;
    }
    
    // Actions
    public function selectCategory($categoryId)
    {
        if ($this->allowMultiple) {
            if (in_array($categoryId, $this->selectedCategories)) {
                $this->removeCategory($categoryId);
            } else {
                $this->selectedCategories[] = $categoryId;
            }
        } else {
            $this->selectedCategories = [$categoryId];
            $this->primaryCategory = $categoryId;
        }
        
        $this->dispatch('categoriesUpdated', [
            'selectedCategories' => $this->selectedCategories,
            'primaryCategory' => $this->primaryCategory
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
        
        $this->dispatch('categoriesUpdated', [
            'selectedCategories' => $this->selectedCategories,
            'primaryCategory' => $this->primaryCategory
        ]);
    }
    
    public function clearAll()
    {
        $this->selectedCategories = [];
        $this->primaryCategory = '';
        
        $this->dispatch('categoriesUpdated', [
            'selectedCategories' => $this->selectedCategories,
            'primaryCategory' => $this->primaryCategory
        ]);
    }
    
    public function toggleShowSelected()
    {
        $this->showSelected = !$this->showSelected;
    }
    
    public function selectAll()
    {
        if ($this->allowMultiple) {
            $this->selectedCategories = $this->filteredCategories->pluck('id')->toArray();
            
            $this->dispatch('categoriesUpdated', [
                'selectedCategories' => $this->selectedCategories,
                'primaryCategory' => $this->primaryCategory
            ]);
        }
    }
    
    public function updatedSearch()
    {
        // Reset show selected when searching
        if (!empty($this->search)) {
            $this->showSelected = false;
        }
    }
    
    public function render()
    {
        return view('livewire.category-selector');
    }
}