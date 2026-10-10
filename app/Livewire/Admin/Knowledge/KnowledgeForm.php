<?php

namespace App\Livewire\Admin\Knowledge;

use App\Models\KnowledgeArticle;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class KnowledgeForm extends Component
{
    use WithFileUploads;

    public ?KnowledgeArticle $article = null;

    public array $form = [
        'slug'       => '',
        'is_active'  => true,
        'sort_order' => 0,
        'title'      => ['uz' => '', 'ru' => '', 'en' => ''],
        'keywords'   => ['uz' => '', 'ru' => '', 'en' => ''],
        'answer'     => ['uz' => '', 'ru' => '', 'en' => ''],
    ];

    public array $newBarcodes = [];
    public array $newManuals = [];

    public function mount(?KnowledgeArticle $article = null): void
    {
        if ($article?->exists) {
            $this->article = $article;
            $this->form = array_merge($this->form, $article->only(['slug', 'is_active', 'sort_order']));
            foreach (['title', 'keywords', 'answer'] as $f) {
                $this->form[$f] = array_merge($this->form[$f], $article->getTranslations($f));
            }
        }
    }

    public function deleteMedia(int $mediaId): void
    {
        $this->article?->media()->whereKey($mediaId)->first()?->delete();
        $this->article?->load('media');
        $this->dispatch('toast', message: 'File removed.', type: 'success');
    }

    public function save(): void
    {
        $id = $this->article?->id;
        $this->validate([
            'form.title.uz'  => 'required|min:3',
            'newBarcodes.*'  => 'image|max:4096',
            'newManuals.*'   => 'mimes:pdf|max:20480',
        ], [
            'form.title.uz.required' => 'Title (Uzbek) is required.',
            'newManuals.*.mimes'     => 'Manuals must be PDF files.',
        ]);

        $this->article ??= new KnowledgeArticle();
        $this->article->fill([
            'is_active'  => $this->form['is_active'],
            'sort_order' => (int) $this->form['sort_order'],
        ]);
        foreach (['title', 'keywords', 'answer'] as $f) {
            $this->article->setTranslations($f, array_filter($this->form[$f], fn ($v) => $v !== '' && $v !== null));
        }
        $this->article->save();

        foreach ($this->newBarcodes as $file) {
            $this->article->addMedia($file->getRealPath())
                ->usingName(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                ->usingFileName(time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->extension())
                ->toMediaCollection('barcodes');
        }
        foreach ($this->newManuals as $file) {
            $this->article->addMedia($file->getRealPath())
                ->usingName(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                ->usingFileName(time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.pdf')
                ->toMediaCollection('manuals');
        }

        session()->flash('success', 'Article saved.');
        $this->redirect(route('admin.knowledge.index'));
    }

    public function render()
    {
        return view('livewire.admin.knowledge.form')->layout('layouts.admin');
    }
}
