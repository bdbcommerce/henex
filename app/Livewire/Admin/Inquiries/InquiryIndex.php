<?php
namespace App\Livewire\Admin\Inquiries;
use App\Models\Inquiry;
use Livewire\Component;
use Livewire\WithPagination;
class InquiryIndex extends Component
{
    use WithPagination;
    public function markRead(int $id): void { Inquiry::findOrFail($id)->update(['is_read' => true]); }
    public function render()
    {
        $inquiries = Inquiry::with('product')->latest()->paginate(25);
        return view('livewire.admin.inquiries.index', compact('inquiries'))->layout('layouts.admin');
    }
}
