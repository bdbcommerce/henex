<?php

namespace App\Livewire\Admin\Industries;

use App\Models\Industry;
use Livewire\Component;

class IndustryIndex extends Component
{
    public function move(int $id, int $direction): void
    {
        $items = Industry::ordered()->get()->values();
        $i = $items->search(fn ($x) => $x->id === $id);
        $j = $i + $direction;
        if ($i === false || ! isset($items[$j])) {
            return;
        }
        [$a, $b] = [$items[$i], $items[$j]];
        $items[$i] = $b;
        $items[$j] = $a;
        $items->each(fn ($x, $pos) => $x->update(['sort_order' => $pos]));
    }

    public function delete(int $id): void
    {
        Industry::findOrFail($id)->delete();   // users / inquiries keep working (FK set null)
        $this->dispatch('toast', message: 'Industry deleted.', type: 'success');
    }

    public function render()
    {
        $items = Industry::ordered()->withCount(['telegramUsers'])->with('categories')->get();

        return view('livewire.admin.industries.index', compact('items'))->layout('layouts.admin');
    }
}
