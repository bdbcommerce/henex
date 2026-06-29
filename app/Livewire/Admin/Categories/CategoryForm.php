<?php
namespace App\Livewire\Admin\Categories;
use App\Models\Category;
use Livewire\Component;
class CategoryForm extends Component
{
    public ?Category $category = null;
    public array $form = ['slug'=>'','is_active'=>true,'sort_order'=>0,'parent_id'=>'',
        'name'=>['uz'=>'','ru'=>'','en'=>''],'description'=>['uz'=>'','ru'=>'','en'=>''],
        'meta_title'=>['uz'=>'','ru'=>'','en'=>''],'meta_description'=>['uz'=>'','ru'=>'','en'=>'']];
    public function mount(?Category $category = null): void
    {
        if ($category?->exists) {
            $this->category = $category;
            $this->form = array_merge($this->form, $category->only(['slug','is_active','sort_order','parent_id']));
            foreach (['name','description','meta_title','meta_description'] as $f) $this->form[$f] = $category->getTranslations($f);
        }
    }
    public function save(): void
    {
        $this->category ??= new Category;
        $this->category->fill(['slug'=>$this->form['slug'],'is_active'=>$this->form['is_active'],'sort_order'=>$this->form['sort_order'],'parent_id'=>$this->form['parent_id']?:null]);
        foreach (['name','description','meta_title','meta_description'] as $f) $this->category->setTranslations($f, $this->form[$f]);
        $this->category->save();
        cache()->forget('nav_categories');
        cache()->forget('featured_categories');
        $this->redirect(route('admin.categories.index'));
    }
    public function render()
    {
        $parents = Category::whereNull('parent_id')->where('id','!=',$this->category?->id ?? 0)->get();
        return view('livewire.admin.categories.form', compact('parents'))->layout('layouts.admin');
    }
}
