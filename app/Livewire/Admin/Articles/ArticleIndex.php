<?php
namespace App\Livewire\Admin\Articles;
use App\Models\Article;
use Livewire\Component;
use Livewire\WithPagination;
class ArticleIndex extends Component
{
    use WithPagination;
    public string $search = '';
    public function updatingSearch(): void { $this->resetPage(); }
    public function delete(int $id): void { Article::findOrFail($id)->delete(); }
    public function render()
    {
        $articles = Article::when($this->search, fn($q) => $q->where('slug','like',"%{$this->search}%"))
            ->orderByDesc('published_at')->paginate(20);
        return view('livewire.admin.articles.index', compact('articles'))->layout('layouts.admin');
    }
}
