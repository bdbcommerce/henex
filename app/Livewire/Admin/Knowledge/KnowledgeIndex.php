<?php

namespace App\Livewire\Admin\Knowledge;

use App\Models\KnowledgeArticle;
use Livewire\Component;
use Livewire\WithPagination;

class KnowledgeIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function toggleActive(int $id): void
    {
        $article = KnowledgeArticle::findOrFail($id);
        $article->update(['is_active' => ! $article->is_active]);
    }

    public function delete(int $id): void
    {
        KnowledgeArticle::findOrFail($id)->delete();   // MediaLibrary removes its files
        $this->dispatch('toast', message: 'Article deleted.', type: 'success');
    }

    public function render()
    {
        $items = KnowledgeArticle::query()
            ->when($this->search, fn ($q) => $q->where(fn ($q) => $q
                ->where('slug', 'like', "%{$this->search}%")
                ->orWhere('title', 'like', "%{$this->search}%")
                ->orWhere('keywords', 'like', "%{$this->search}%")))
            ->with('media')
            ->orderBy('sort_order')->latest('id')
            ->paginate(20);

        return view('livewire.admin.knowledge.index', compact('items'))->layout('layouts.admin');
    }
}
