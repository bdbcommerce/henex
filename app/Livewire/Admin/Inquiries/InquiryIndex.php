<?php
namespace App\Livewire\Admin\Inquiries;
use App\Models\Inquiry;
use Livewire\Component;
use Livewire\WithPagination;
class InquiryIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingStatus(): void { $this->resetPage(); }

    public function markRead(int $id): void
    {
        Inquiry::findOrFail($id)->update(['is_read' => true]);
        $this->dispatch('toast', message: 'Inquiry marked as read.', type: 'success');
    }

    public function render()
    {
        $inquiries = Inquiry::query()
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('phone', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('company', 'like', "%{$this->search}%");
            }))
            ->when($this->status === 'unread', fn ($q) => $q->where('is_read', false))
            ->when($this->status === 'read', fn ($q) => $q->where('is_read', true))
            ->with('product')
            ->latest()
            ->paginate(25);

        return view('livewire.admin.inquiries.index', compact('inquiries'))->layout('layouts.admin');
    }
}
