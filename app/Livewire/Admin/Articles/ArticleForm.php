<?php
namespace App\Livewire\Admin\Articles;
use App\Models\Article;
use Livewire\Component;
use Livewire\WithFileUploads;
class ArticleForm extends Component
{
    use WithFileUploads;

    public ?Article $article = null;
    public array $form = ['slug'=>'','type'=>'news','is_published'=>false,'published_at'=>'',
        'title'=>['uz'=>'','ru'=>'','en'=>''],'excerpt'=>['uz'=>'','ru'=>'','en'=>''],
        'content'=>['uz'=>'','ru'=>'','en'=>''],'meta_title'=>['uz'=>'','ru'=>'','en'=>''],'meta_description'=>['uz'=>'','ru'=>'','en'=>'']];
    public $cover = null;
    public function mount(?Article $article = null): void
    {
        if ($article?->exists) {
            $this->article = $article;
            $this->form = array_merge($this->form, $article->only(['slug','type','is_published']));
            $this->form['published_at'] = $article->published_at?->format('Y-m-d\TH:i');
            foreach (['title','excerpt','content','meta_title','meta_description'] as $f) $this->form[$f] = $article->getTranslations($f);
        }
    }
    public function save(): void
    {
        $articleId = $this->article?->id;

        $this->validate([
            'form.title.uz' => 'required|min:2',
            'form.type'     => 'required|in:news,blog,guide',
            'cover'         => 'nullable|image|max:4096',
        ], [
            'form.title.uz.required' => 'Title (Uzbek) is required.',
        ]);

        // If publishing without an explicit date, default to now so it actually
        // appears on the frontend (Article::scopePublished() requires published_at).
        $publishedAt = $this->form['published_at']
            ?: ($this->form['is_published'] ? now()->format('Y-m-d\TH:i') : null);

        $this->article ??= new Article;
        $this->article->fill([
            'type'         => $this->form['type'],
            'is_published' => $this->form['is_published'],
            'user_id'      => $this->article->user_id ?? auth()->id(),
            'published_at' => $publishedAt ?: null,
        ]);
        foreach (['title','excerpt','content','meta_title','meta_description'] as $f) $this->article->setTranslations($f, $this->form[$f]);
        $this->article->save();

        if ($this->cover) {
            $this->article->addMedia($this->cover->getRealPath())
                ->usingFileName(time() . '_' . $this->cover->getClientOriginalName())
                ->toMediaCollection('cover');
        }

        session()->flash('success', 'Article saved successfully.');
        $this->redirect(route('admin.articles.index'));
    }

    public function removeCover(): void
    {
        $this->article?->clearMediaCollection('cover');
        $this->dispatch('toast', message: 'Cover image removed.', type: 'success');
    }

    public function render()
    {
        return view('livewire.admin.articles.form')->layout('layouts.admin');
    }
}
