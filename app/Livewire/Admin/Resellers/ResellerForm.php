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
        $this->reseller ??= new Reseller;
        $this->reseller->fill(array_diff_key($this->form, ['address'=>'']));
        $this->reseller->setTranslations('address', $this->form['address']);
        $this->reseller->save();
        $this->redirect(route('admin.resellers.index'));
    }
    public function render()
    {
        $regions = Region::orderBy('sort_order')->get();
        return view('livewire.admin.resellers.form', compact('regions'))->layout('layouts.admin');
    }
}
