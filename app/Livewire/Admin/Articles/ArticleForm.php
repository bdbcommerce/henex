<?php
namespace App\Livewire\Admin\Articles;
use App\Models\Article;
use Livewire\Component;
class ArticleForm extends Component
{
    public ?Article $article = null;
    public array $form = ['slug'=>'','type'=>'news','is_published'=>false,'published_at'=>'',
        'title'=>['uz'=>'','ru'=>'','en'=>''],'excerpt'=>['uz'=>'','ru'=>'','en'=>''],
        'content'=>['uz'=>'','ru'=>'','en'=>''],'meta_title'=>['uz'=>'','ru'=>'','en'=>''],'meta_description'=>['uz'=>'','ru'=>'','en'=>'']];
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
        $this->article ??= new Article;
        $this->article->fill(['slug'=>$this->form['slug'],'type'=>$this->form['type'],'is_published'=>$this->form['is_published'],'author_id'=>auth()->id(),'published_at'=>$this->form['published_at']?:null]);
        foreach (['title','excerpt','content','meta_title','meta_description'] as $f) $this->article->setTranslations($f, $this->form[$f]);
        $this->article->save();
        $this->redirect(route('admin.articles.index'));
    }
    public function render()
    {
        return view('livewire.admin.articles.form')->layout('layouts.admin');
    }
}
