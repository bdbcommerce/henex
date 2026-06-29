<?php
namespace App\Livewire\Frontend;
use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
class ProductCatalog extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $category = '';

    #[Url]
    public string $sort = 'sort_order';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingCategory(): void { $this->resetPage(); }

    public function mount(string $categorySlug = ''): void
    {
        $this->category = $categorySlug;
    }

    public function render()
    {
        $query = Product::where('is_active', true)->with(['media', 'categories']);

        if ($this->search) {
            $locale = app()->getLocale();
            $query->where(fn ($q) =>
                $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.\"{$locale}\"')) LIKE ?", ["%{$this->search}%"])
                  ->orWhere('sku', 'like', "%{$this->search}%")
            );
        }

        if ($this->category) {
            $query->whereHas('categories', fn ($q) => $q->where('slug', $this->category));
        }

        $query->orderBy($this->sort === 'newest' ? 'created_at' : 'sort_order',
                        $this->sort === 'name_desc' ? 'desc' : 'asc');

        $products = $query->paginate(12);

        $categories = cache()->remember('nav_categories', 600, fn () =>
            Category::where('is_active', true)->whereNull('parent_id')
                ->with('children')->orderBy('sort_order')->get()
        );

        return view('livewire.frontend.product-catalog', compact('products', 'categories'));
    }
}
