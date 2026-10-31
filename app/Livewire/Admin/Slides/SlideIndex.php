<?php

namespace App\Livewire\Admin\Slides;

use App\Models\Slide;
use Livewire\Component;
use Livewire\WithFileUploads;

class SlideIndex extends Component
{
    use WithFileUploads;

    public bool $showForm = false;
    public ?int $editingId = null;

    public array $form = [
        'title'       => ['uz' => '', 'ru' => '', 'en' => ''],
        'subtitle'    => ['uz' => '', 'ru' => '', 'en' => ''],
        'button_text' => ['uz' => '', 'ru' => '', 'en' => ''],
        'link'        => '',
        'sort_order'  => 0,
        'is_active'   => true,
    ];

    public $image = null;

    public function openCreate(): void
    {
        $this->resetForm();
        $this->editingId = null;
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $slide = Slide::findOrFail($id);
        $this->editingId = $id;
        $this->form = [
            'title'       => $slide->getTranslations('title'),
            'subtitle'    => $slide->getTranslations('subtitle'),
            'button_text' => $slide->getTranslations('button_text'),
            'link'        => $slide->link ?? '',
            'sort_order'  => $slide->sort_order,
            'is_active'   => $slide->is_active,
        ];
        $this->image = null;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'form.title.uz' => 'required|min:2',
            'image'         => $this->editingId ? 'nullable|image|max:4096' : 'required|image|max:4096',
        ], [
            'form.title.uz.required' => 'Title (Uzbek) is required.',
            'image.required'         => 'Please upload an image.',
        ]);

        $slide = $this->editingId ? Slide::findOrFail($this->editingId) : new Slide();
        $slide->fill([
            'link'        => $this->form['link'] ?: null,
            'sort_order'  => $this->form['sort_order'],
            'is_active'   => $this->form['is_active'],
        ]);
        foreach (['title', 'subtitle', 'button_text'] as $field) {
            $slide->setTranslations($field, $this->form[$field]);
        }
        $slide->save();

        if ($this->image) {
            $slide->addMedia($this->image->getRealPath())
                ->usingFileName(time() . '_' . $this->image->getClientOriginalName())
                ->toMediaCollection('slide');
        }

        cache()->forget('slides');
        $this->showForm = false;
        $this->resetForm();
        $this->dispatch('toast', message: 'Slide saved.', type: 'success');
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function toggleActive(int $id): void
    {
        $slide = Slide::findOrFail($id);
        $slide->update(['is_active' => ! $slide->is_active]);
        cache()->forget('slides');
        $this->dispatch('toast', message: $slide->is_active ? 'Slide shown on homepage.' : 'Slide hidden from homepage.', type: 'success');
    }

    public function moveUp(int $id): void
    {
        $slide = Slide::findOrFail($id);
        $prev  = Slide::where('sort_order', '<', $slide->sort_order)->orderByDesc('sort_order')->first();
        if ($prev) {
            [$slide->sort_order, $prev->sort_order] = [$prev->sort_order, $slide->sort_order];
            $slide->save();
            $prev->save();
            cache()->forget('slides');
        }
    }

    public function moveDown(int $id): void
    {
        $slide = Slide::findOrFail($id);
        $next  = Slide::where('sort_order', '>', $slide->sort_order)->orderBy('sort_order')->first();
        if ($next) {
            [$slide->sort_order, $next->sort_order] = [$next->sort_order, $slide->sort_order];
            $slide->save();
            $next->save();
            cache()->forget('slides');
        }
    }

    public function delete(int $id): void
    {
        Slide::findOrFail($id)->delete();
        cache()->forget('slides');
        $this->dispatch('toast', message: 'Slide deleted.', type: 'success');
    }

    public function render()
    {
        $slides = Slide::orderBy('sort_order')->get();
        return view('livewire.admin.slides.index', compact('slides'))->layout('layouts.admin');
    }

    private function resetForm(): void
    {
        $this->form = [
            'title'       => ['uz' => '', 'ru' => '', 'en' => ''],
            'subtitle'    => ['uz' => '', 'ru' => '', 'en' => ''],
            'button_text' => ['uz' => '', 'ru' => '', 'en' => ''],
            'link'        => '',
            'sort_order'  => 0,
            'is_active'   => true,
        ];
        $this->image = null;
    }
}
