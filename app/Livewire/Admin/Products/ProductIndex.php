<?php

namespace App\Livewire\Admin\Products;

use App\Models\Product;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Products')]
class ProductIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public string $sortBy = 'newest';
    public int $perPage = 20;

    protected $queryString = ['search', 'status', 'sortBy', 'perPage'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        Product::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'Product deleted successfully.', type: 'success');
    }

    public function toggleActive(int $id): void
    {
        $product = Product::findOrFail($id);
        $product->update(['is_active' => !$product->is_active]);
        $this->dispatch('toast', message: 'Product updated.', type: 'success');
    }

    public function render()
    {
        $query = Product::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('slug', 'like', "%{$this->search}%")
                  ->orWhere('sku', 'like', "%{$this->search}%");
            });
        }

        if ($this->status) {
            $query->where('is_active', $this->status === 'active');
        }

        $query = match($this->sortBy) {
            'oldest' => $query->orderBy('created_at'),
            'featured' => $query->where('is_featured', true)->orderByDesc('created_at'),
            default => $query->orderByDesc('created_at'),
        };

        $products = $query->paginate($this->perPage);

        return view('livewire.admin.products.index', compact('products'))
            ->layout('layouts.admin');
    }
}
