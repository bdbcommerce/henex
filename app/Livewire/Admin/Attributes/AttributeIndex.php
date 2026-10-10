<?php

namespace App\Livewire\Admin\Attributes;

use App\Models\Attribute;
use Livewire\Component;
use Livewire\WithPagination;

class AttributeIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        Attribute::findOrFail($id)->delete();   // values + product links cascade
        $this->dispatch('toast', message: 'Attribute deleted.', type: 'success');
    }

    public function render()
    {
        $items = Attribute::query()
            ->when($this->search, fn ($q) => $q->where(fn ($q) => $q
                ->where('slug', 'like', "%{$this->search}%")
                ->orWhere('name', 'like', "%{$this->search}%")))
            ->with('values')
            ->ordered()
            ->paginate(20);

        return view('livewire.admin.attributes.index', compact('items'))->layout('layouts.admin');
    }
}
