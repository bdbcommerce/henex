<?php
namespace App\Livewire\Admin\Resellers;
use App\Models\Reseller;
use Livewire\Component;
use Livewire\WithPagination;
class ResellerIndex extends Component
{
    use WithPagination;
    public string $search = '';
    public function updatingSearch(): void { $this->resetPage(); }
    public function delete(int $id): void { Reseller::findOrFail($id)->delete(); }
    public function render()
    {
        $resellers = Reseller::when($this->search, fn($q) => $q->where('name','like',"%{$this->search}%"))
            ->with('region')->orderBy('sort_order')->paginate(20);
        return view('livewire.admin.resellers.index', compact('resellers'))->layout('layouts.admin');
    }
}
