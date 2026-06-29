<?php
namespace App\Livewire\Admin\Slides;
use App\Models\Slide;
use Livewire\Component;
class SlideIndex extends Component
{
    public function toggleActive(int $id): void
    {
        $slide = Slide::findOrFail($id);
        $slide->update(['is_active' => ! $slide->is_active]);
        cache()->forget('slides');
    }
    public function delete(int $id): void
    {
        Slide::findOrFail($id)->delete();
        cache()->forget('slides');
    }
    public function render()
    {
        $slides = Slide::orderBy('sort_order')->get();
        return view('livewire.admin.slides.index', compact('slides'))->layout('layouts.admin');
    }
}
