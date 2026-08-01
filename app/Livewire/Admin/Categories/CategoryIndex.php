<?php
namespace App\Livewire\Admin\Categories;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;
class CategoryIndex extends Component
{
    use WithPagination;
    public string $search = '';
    public function updatingSearch(): void { $this->resetPage(); }
    public function delete(int $id): void
    {
        $category = Category::withCount('children', 'products')->findOrFail($id);

        if ($category->children_count > 0) {
            $this->dispatch('toast', message: "Can't delete: this category has {$category->children_count} sub-categor" . ($category->children_count === 1 ? 'y' : 'ies') . '. Reassign or delete them first.', type: 'error');
            return;
        }

        $productCount = $category->products_count;
        $category->delete();

        $message = $productCount > 0
            ? "Category deleted. It was unlinked from {$productCount} product" . ($productCount === 1 ? '' : 's') . '.'
            : 'Category deleted successfully.';
        $this->dispatch('toast', message: $message, type: 'success');
    }
    public function render()
    {
        $categories = Category::when($this->search, fn($q) => $q->where('slug','like',"%{$this->search}%"))
            ->whereNull('parent_id')->with(['children', 'media'])->orderBy('sort_order')->paginate(20);
        return view('livewire.admin.categories.index', compact('categories'))->layout('layouts.admin');
    }
}
