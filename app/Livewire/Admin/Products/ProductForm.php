<?php
namespace App\Livewire\Admin\Products;
use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithFileUploads;
class ProductForm extends Component
{
    use WithFileUploads;
    public ?Product $product = null;
    public array $form = ['sku' => '', 'slug' => '', 'is_active' => true, 'is_featured' => false, 'sort_order' => 0,
        'name' => ['uz' => '', 'ru' => '', 'en' => ''],
        'short_description' => ['uz' => '', 'ru' => '', 'en' => ''],
        'description' => ['uz' => '', 'ru' => '', 'en' => ''],
        'meta_title' => ['uz' => '', 'ru' => '', 'en' => ''],
        'meta_description' => ['uz' => '', 'ru' => '', 'en' => ''],
    ];
    public array $specs = [];
    public array $selectedCategories = [];
    public function mount(?Product $product = null): void
    {
        if ($product?->exists) {
            $this->product = $product;
            $this->form = array_merge($this->form, $product->only(['sku','slug','is_active','is_featured','sort_order']));
            foreach (['name','short_description','description','meta_title','meta_description'] as $f) {
                $this->form[$f] = $product->getTranslations($f);
            }
            $this->selectedCategories = $product->categories->pluck('id')->toArray();
            $this->specs = $product->specifications->map(fn($s) => ['key' => $s->getTranslations('key'), 'value' => $s->getTranslations('value'), 'sort_order' => $s->sort_order])->toArray();
        }
    }
    public function addSpec(): void { $this->specs[] = ['key' => ['uz'=>'','ru'=>'','en'=>''], 'value' => ['uz'=>'','ru'=>'','en'=>''], 'sort_order' => count($this->specs)]; }
    public function removeSpec(int $i): void { array_splice($this->specs, $i, 1); }
    public function save(): void
    {
        $this->product ??= new Product;
        $this->product->fill(['sku' => $this->form['sku'], 'slug' => $this->form['slug'], 'is_active' => $this->form['is_active'], 'is_featured' => $this->form['is_featured'], 'sort_order' => $this->form['sort_order']]);
        foreach (['name','short_description','description','meta_title','meta_description'] as $f) {
            $this->product->setTranslations($f, $this->form[$f]);
        }
        $this->product->save();
        $this->product->categories()->sync($this->selectedCategories);
        $this->product->specifications()->delete();
        foreach ($this->specs as $i => $spec) {
            $s = $this->product->specifications()->create(['sort_order' => $i]);
            $s->setTranslations('key', $spec['key'])->setTranslations('value', $spec['value'])->save();
        }
        session()->flash('success', 'Product saved.');
        $this->redirect(route('admin.products.index'));
    }
    public function render()
    {
        $categories = Category::where('is_active', true)->whereNull('parent_id')->with('children')->get();
        return view('livewire.admin.products.form', compact('categories'))->layout('layouts.admin');
    }
}
