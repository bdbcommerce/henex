<?php
namespace App\Livewire\Admin\Resellers;
use App\Models\Region;
use App\Models\Reseller;
use Livewire\Component;
class ResellerForm extends Component
{
    public ?Reseller $reseller = null;
    public array $form = ['region_id'=>'','name'=>'','type'=>'reseller','phone'=>'','phone2'=>'','email'=>'','website'=>'','is_active'=>true,'sort_order'=>0,'address'=>['uz'=>'','ru'=>'','en'=>''],'latitude'=>'','longitude'=>''];
    public function mount(?Reseller $reseller = null): void
    {
        if ($reseller?->exists) {
            $this->reseller = $reseller;
            $this->form = array_merge($this->form, $reseller->only(['region_id','name','type','phone','phone2','email','website','is_active','sort_order','latitude','longitude']));
            $this->form['address'] = $reseller->getTranslations('address');
        }
    }
    public function save(): void
    {
        $this->validate([
            'form.name'      => 'required|min:2',
            'form.region_id' => 'required|exists:regions,id',
            'form.type'      => 'required|in:reseller,service_center,both',
            'form.email'     => 'nullable|email',
            'form.website'   => 'nullable|url',
            'form.latitude'  => 'nullable|numeric|between:-90,90',
            'form.longitude' => 'nullable|numeric|between:-180,180',
        ], [
            'form.name.required'      => 'Reseller name is required.',
            'form.region_id.required' => 'Please select a region.',
        ]);

        $this->reseller ??= new Reseller;
        $attributes = array_diff_key($this->form, ['address' => '']);
        // Empty strings are invalid for nullable decimal columns under MySQL strict mode — coerce to null.
        $attributes['latitude'] = $attributes['latitude'] !== '' ? $attributes['latitude'] : null;
        $attributes['longitude'] = $attributes['longitude'] !== '' ? $attributes['longitude'] : null;
        $attributes['region_id'] = $attributes['region_id'] !== '' ? $attributes['region_id'] : null;
        $this->reseller->fill($attributes);
        $this->reseller->setTranslations('address', $this->form['address']);
        $this->reseller->save();
        session()->flash('success', 'Reseller saved successfully.');
        $this->redirect(route('admin.resellers.index'));
    }
    public function render()
    {
        $regions = Region::orderBy('sort_order')->get();
        return view('livewire.admin.resellers.form', compact('regions'))->layout('layouts.admin');
    }
}
