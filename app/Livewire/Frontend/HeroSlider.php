<?php
namespace App\Livewire\Frontend;
use App\Models\Slide;
use Livewire\Component;
class HeroSlider extends Component
{
    public function render()
    {
        $slides = cache()->remember('slides', 600, fn () =>
            Slide::where('is_active', true)->orderBy('sort_order')->get()
        );
        return view('livewire.frontend.hero-slider', compact('slides'));
    }
}
