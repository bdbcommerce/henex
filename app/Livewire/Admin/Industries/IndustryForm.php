<?php

namespace App\Livewire\Admin\Industries;

use App\Models\Category;
use App\Models\Industry;
use Illuminate\Support\Str;
use Livewire\Component;

class IndustryForm extends Component
{
    public ?Industry $industry = null;

    public array $form = [
        'slug'       => '',
        'emoji'      => '',
        'is_active'  => true,
        'sort_order' => 0,
        'name'       => ['uz' => '', 'ru' => '', 'en' => ''],
    ];

    public array $selectedCategories = [];

    public function mount(?Industry $industry = null): void
    {
        if ($industry?->exists) {
            $this->industry = $industry;
            $this->form = array_merge($this->form, $industry->only(['slug', 'emoji', 'is_active', 'sort_order']));
            $this->form['emoji'] ??= '';
            $this->form['name'] = array_merge($this->form['name'], $industry->getTranslations('name'));
            $this->selectedCategories = $industry->categories()->pluck('categories.id')->all();
        } else {
            $this->form['sort_order'] = (int) Industry::max('sort_order') + 1;
        }
    }

    public function save(): void
    {
        $id = $this->industry?->id;
        $this->validate([
            'form.name.uz' => 'required|min:2',
            'form.emoji'   => 'nullable|string|max:16',
            'selectedCategories.*' => 'exists:categories,id',
        ], ['form.name.uz.required' => 'Name (Uzbek) is required.']);

        $this->industry ??= new Industry();
        $this->industry->fill([
            'emoji'      => $this->form['emoji'] ?: null,
            'is_active'  => $this->form['is_active'],
            'sort_order' => (int) $this->form['sort_order'],
            'name'       => array_filter($this->form['name']),
        ])->save();
        $this->industry->categories()->sync($this->selectedCategories);

        session()->flash('success', 'Industry saved.');
        $this->redirect(route('admin.industries.index'));
    }

    public function render()
    {
        $categories = Category::whereNull('parent_id')->with('children')->orderBy('sort_order')->get();

        return view('livewire.admin.industries.form', compact('categories'))->layout('layouts.admin');
    }
}
