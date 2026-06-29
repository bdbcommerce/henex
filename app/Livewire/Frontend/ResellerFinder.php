<?php
namespace App\Livewire\Frontend;
use App\Models\Region;
use App\Models\Reseller;
use Livewire\Attributes\Url;
use Livewire\Component;
class ResellerFinder extends Component
{
    #[Url]
    public string $region = '';

    #[Url]
    public string $type = '';

    public function mount(string $regionSlug = ''): void
    {
        $this->region = $regionSlug;
    }

    public function render()
    {
        $regions = cache()->remember('all_regions', 3600, fn () =>
            Region::orderBy('sort_order')->get()
        );

        $query = Reseller::where('is_active', true)->with('region')->orderBy('sort_order');

        if ($this->region) {
            $query->whereHas('region', fn ($q) => $q->where('slug', $this->region));
        }
        if ($this->type) {
            $query->where(fn ($q) => $q->where('type', $this->type)->orWhere('type', 'both'));
        }

        $resellers = $query->get();

        return view('livewire.frontend.reseller-finder', compact('regions', 'resellers'));
    }
}
