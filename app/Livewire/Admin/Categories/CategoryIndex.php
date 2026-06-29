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
    public function delete(int $id): void { Category::findOrFail($id)->delete(); }
    public function render()
    {
        $categories = Category::when($this->search, fn($q) => $q->where('slug','like',"%{$this->search}%"))
            ->whereNull('parent_id')->with('children')->orderBy('sort_order')->paginate(20);
        return view('livewire.admin.categories.index', compact('categories'))->layout('layouts.admin');
    }
}
