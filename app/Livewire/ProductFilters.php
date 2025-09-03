<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\ProductFilter;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class ProductFilters extends Component
{
    use WithPagination;

    // Filter properties
    public $selectedFilters = [];
    public $priceRange = ['min' => 0, 'max' => 0];
    public $selectedPriceRange = ['min' => 0, 'max' => 0];
    public $selectedCategory = null;
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 12;
    public $search = '';

    // UI properties
    public $showFilters = true;
    public $viewMode = 'grid'; // grid or list

    protected $queryString = [
        'selectedFilters' => ['except' => []],
        'selectedPriceRange' => ['except' => ['min' => 0, 'max' => 0]],
        'selectedCategory' => ['except' => null],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'search' => ['except' => ''],
        'page' => ['except' => 1]
    ];

    protected $listeners = [
        'filterUpdated' => '$refresh',
        'categorySelected' => 'setCategory'
    ];

    public function mount()
    {
        $this->initializePriceRange();
        $this->resetPage();
    }

    public function render()
    {
        $products = $this->getFilteredProducts();
        $filters = $this->getAvailableFilters();
        $categories = Category::active()->orderBy('name')->get();
        $stats = $this->getFilterStats();

        return view('livewire.product-filters', [
            'products' => $products,
            'filters' => $filters,
            'categories' => $categories,
            'stats' => $stats,
            'sortOptions' => $this->getSortOptions()
        ]);
    }

    public function getFilteredProducts()
    {
        $query = Product::with(['categories', 'featuredImage', 'filterValues.filter'])
                       ->active()
                       ->when($this->search, function($q) {
                           $q->where(function($query) {
                               $query->where('name', 'like', "%{$this->search}%")
                                     ->orWhere('description', 'like', "%{$this->search}%")
                                     ->orWhere('sku', 'like', "%{$this->search}%");
                           });
                       })
                       ->when($this->selectedCategory, function($q) {
                           $q->whereHas('categories', function($categoryQuery) {
                               $categoryQuery->where('categories.id', $this->selectedCategory);
                           });
                       })
                       ->when(!empty($this->selectedFilters), function($q) {
                           $q->withFilters($this->selectedFilters);
                       })
                       ->when($this->selectedPriceRange['min'] > 0 || $this->selectedPriceRange['max'] > 0, function($q) {
                           $min = $this->selectedPriceRange['min'] ?: 0;
                           $max = $this->selectedPriceRange['max'] ?: PHP_INT_MAX;
                           $q->withPriceRange($min, $max);
                       });

        return $query->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
    }

    public function getAvailableFilters()
    {
        return Cache::remember('product_filters_' . md5(serialize($this->selectedFilters)), 300, function() {
            return ProductFilter::active()
                              ->ordered()
                              ->with(['values' => function($query) {
                                  $query->select('filter_id', 'value', 'display_value')
                                        ->distinct()
                                        ->orderBy('display_value');
                              }])
                              ->get()
                              ->map(function($filter) {
                                  $filter->unique_values = $filter->getUniqueValues();
                                  if ($filter->type === ProductFilter::TYPE_PRICE) {
                                      $filter->price_range = $filter->getPriceRange();
                                  }
                                  return $filter;
                              });
        });
    }

    public function getFilterStats()
    {
        $totalProducts = Product::active()->count();
        $filteredProducts = $this->getFilteredProducts()->total();
        
        return [
            'total' => $totalProducts,
            'filtered' => $filteredProducts,
            'showing' => min($this->perPage, $filteredProducts)
        ];
    }

    public function getSortOptions()
    {
        return [
            'created_at' => 'جدیدترین',
            'name' => 'نام محصول',
            'price' => 'قیمت',
            'popularity' => 'محبوبیت',
            'rating' => 'امتیاز'
        ];
    }

    public function initializePriceRange()
    {
        $priceRange = Cache::remember('product_price_range', 3600, function() {
            $prices = Product::active()
                           ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
                           ->first();
            
            return [
                'min' => $prices->min_price ?? 0,
                'max' => $prices->max_price ?? 0
            ];
        });

        $this->priceRange = $priceRange;
        
        if ($this->selectedPriceRange['min'] == 0 && $this->selectedPriceRange['max'] == 0) {
            $this->selectedPriceRange = $priceRange;
        }
    }

    public function updateFilter($filterId, $value, $checked = true)
    {
        if (!isset($this->selectedFilters[$filterId])) {
            $this->selectedFilters[$filterId] = [];
        }

        if ($checked) {
            if (!in_array($value, $this->selectedFilters[$filterId])) {
                $this->selectedFilters[$filterId][] = $value;
            }
        } else {
            $this->selectedFilters[$filterId] = array_filter(
                $this->selectedFilters[$filterId],
                fn($v) => $v !== $value
            );
            
            if (empty($this->selectedFilters[$filterId])) {
                unset($this->selectedFilters[$filterId]);
            }
        }

        $this->resetPage();
        $this->dispatch('filterUpdated');
    }

    public function updateSingleFilter($filterId, $value)
    {
        if ($value) {
            $this->selectedFilters[$filterId] = [$value];
        } else {
            unset($this->selectedFilters[$filterId]);
        }

        $this->resetPage();
        $this->dispatch('filterUpdated');
    }

    public function updatePriceRange()
    {
        $this->resetPage();
        $this->dispatch('filterUpdated');
    }

    public function setCategory($categoryId)
    {
        $this->selectedCategory = $categoryId;
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->selectedFilters = [];
        $this->selectedPriceRange = $this->priceRange;
        $this->selectedCategory = null;
        $this->search = '';
        $this->resetPage();
        $this->dispatch('filterUpdated');
    }

    public function clearFilter($filterId)
    {
        unset($this->selectedFilters[$filterId]);
        $this->resetPage();
        $this->dispatch('filterUpdated');
    }

    public function setSortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    public function setPerPage($perPage)
    {
        $this->perPage = $perPage;
        $this->resetPage();
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function isFilterSelected($filterId, $value)
    {
        return isset($this->selectedFilters[$filterId]) && 
               in_array($value, $this->selectedFilters[$filterId]);
    }

    public function getSelectedFilterCount()
    {
        $count = 0;
        foreach ($this->selectedFilters as $values) {
            $count += count($values);
        }
        
        if ($this->selectedPriceRange['min'] > $this->priceRange['min'] || 
            $this->selectedPriceRange['max'] < $this->priceRange['max']) {
            $count++;
        }
        
        if ($this->selectedCategory) {
            $count++;
        }
        
        return $count;
    }

    public function getActiveFiltersText()
    {
        $filters = [];
        
        foreach ($this->selectedFilters as $filterId => $values) {
            $filter = ProductFilter::find($filterId);
            if ($filter) {
                $filterValues = [];
                foreach ($values as $value) {
                    $filterValue = $filter->values()->where('value', $value)->first();
                    $filterValues[] = $filterValue ? $filterValue->getDisplayLabel() : $value;
                }
                $filters[] = $filter->name . ': ' . implode(', ', $filterValues);
            }
        }
        
        if ($this->selectedCategory) {
            $category = Category::find($this->selectedCategory);
            if ($category) {
                $filters[] = 'دسته‌بندی: ' . $category->name;
            }
        }
        
        if ($this->selectedPriceRange['min'] > $this->priceRange['min'] || 
            $this->selectedPriceRange['max'] < $this->priceRange['max']) {
            $filters[] = 'قیمت: ' . number_format($this->selectedPriceRange['min']) . 
                        ' - ' . number_format($this->selectedPriceRange['max']) . ' تومان';
        }
        
        return implode(' | ', $filters);
    }
}