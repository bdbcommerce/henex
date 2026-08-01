<?php

namespace App\Livewire\Admin\Products;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductForm extends Component
{
    use WithFileUploads;

    public ?Product $product = null;

    public array $form = [
        'sku'               => '',
        'slug'              => '',
        'is_active'         => true,
        'is_featured'       => false,
        'sort_order'        => 0,
        'name'              => ['uz' => '', 'ru' => '', 'en' => ''],
        'short_description' => ['uz' => '', 'ru' => '', 'en' => ''],
        'description'       => ['uz' => '', 'ru' => '', 'en' => ''],
        'meta_title'        => ['uz' => '', 'ru' => '', 'en' => ''],
        'meta_description'  => ['uz' => '', 'ru' => '', 'en' => ''],
    ];

    public array $specs = [];
    public array $selectedCategories = [];
    public array $existingImages = [];
    public array $newImages = [];

    public bool $showMediaBrowser = false;
    public ?int $browsingProductId = null;
    public array $pickedMedia = [];

    public function mount(?Product $product = null): void
    {
        if ($product?->exists) {
            $this->product = $product;
            $this->form = array_merge($this->form, $product->only(['sku', 'slug', 'is_active', 'is_featured', 'sort_order']));
            foreach (['name', 'short_description', 'description', 'meta_title', 'meta_description'] as $field) {
                $this->form[$field] = $product->getTranslations($field);
            }
            $this->selectedCategories = $product->categories->pluck('id')->toArray();
            $this->specs = $product->specifications
                ->map(fn($s) => [
                    'key'        => $s->getTranslations('key'),
                    'value'      => $s->getTranslations('value'),
                    'sort_order' => $s->sort_order,
                ])->toArray();
            $this->loadExistingImages();
        }
    }

    public function addSpec(): void
    {
        $this->specs[] = [
            'key'        => ['uz' => '', 'ru' => '', 'en' => ''],
            'value'      => ['uz' => '', 'ru' => '', 'en' => ''],
            'sort_order' => count($this->specs),
        ];
    }

    public function removeSpec(int $i): void
    {
        array_splice($this->specs, $i, 1);
    }

    public function deleteImage(int $mediaId): void
    {
        $this->product?->getMedia('gallery')->find($mediaId)?->delete();
        $this->loadExistingImages();
        $this->dispatch('toast', message: 'Image deleted.', type: 'success');
    }

    public function openMediaBrowser(): void
    {
        $this->browsingProductId = null;
        $this->showMediaBrowser = true;
    }

    public function closeMediaBrowser(): void
    {
        $this->showMediaBrowser = false;
        $this->browsingProductId = null;
    }

    public function browseFolder(?int $productId): void
    {
        $this->browsingProductId = $productId;
    }

    public function togglePick(int $mediaId): void
    {
        $media = Media::find($mediaId);
        if (! $media) {
            return;
        }

        if (isset($this->pickedMedia[$mediaId])) {
            unset($this->pickedMedia[$mediaId]);
            return;
        }

        $this->pickedMedia[$mediaId] = [
            'id'   => $media->id,
            'url'  => $media->getUrl('thumb') ?: $media->getUrl(),
            'name' => $media->file_name,
        ];
    }

    public function removePicked(int $mediaId): void
    {
        unset($this->pickedMedia[$mediaId]);
    }

    public function save(): void
    {
        $productId = $this->product?->id;

        $this->validate([
            'form.name.uz' => 'required|min:2',
            'form.slug'    => 'required|alpha_dash|unique:products,slug' . ($productId ? ",{$productId}" : ''),
            'form.sku'     => 'nullable|unique:products,sku' . ($productId ? ",{$productId}" : ''),
            'newImages.*'  => 'nullable|image|max:4096',
        ], [
            'form.name.uz.required' => 'Product name (Uzbek) is required.',
            'form.slug.required'    => 'Slug is required.',
            'form.slug.unique'      => 'This slug is already taken.',
            'form.sku.unique'       => 'This SKU is already in use.',
        ]);

        $this->product ??= new Product();
        $this->product->fill([
            'sku'         => $this->form['sku'] ?: null,
            'slug'        => $this->form['slug'],
            'is_active'   => $this->form['is_active'],
            'is_featured' => $this->form['is_featured'],
            'sort_order'  => $this->form['sort_order'],
        ]);
        foreach (['name', 'short_description', 'description', 'meta_title', 'meta_description'] as $field) {
            $this->product->setTranslations($field, $this->form[$field]);
        }
        $this->product->save();

        $this->product->categories()->sync($this->selectedCategories);

        $this->product->specifications()->delete();
        foreach ($this->specs as $i => $spec) {
            $s = $this->product->specifications()->make(['sort_order' => $i]);
            $s->setTranslations('key', $spec['key']);
            $s->setTranslations('value', $spec['value']);
            $s->save();
        }

        foreach ($this->newImages as $file) {
            $this->product
                ->addMedia($file->getRealPath())
                ->usingFileName(time() . '_' . $file->getClientOriginalName())
                ->toMediaCollection('gallery');
        }

        foreach ($this->pickedMedia as $mediaId => $meta) {
            $source = Media::find($mediaId);
            if (! $source) {
                continue;
            }
            $this->product
                ->addMedia($source->getPath())
                ->preservingOriginal()
                ->usingFileName(time() . '_' . $source->file_name)
                ->toMediaCollection('gallery');
        }
        $this->pickedMedia = [];

        session()->flash('success', 'Product saved successfully.');
        $this->redirect(route('admin.products.index'), navigate: false);
    }

    public function render()
    {
        $categories = Category::where('is_active', true)->whereNull('parent_id')->with('children')->get();

        $mediaFolders = [];
        $folderMedia = [];

        if ($this->showMediaBrowser) {
            if ($this->browsingProductId) {
                $folderProduct = Product::find($this->browsingProductId);
                $folderMedia = $folderProduct
                    ? $folderProduct->getMedia('gallery')->map(fn ($m) => [
                        'id'   => $m->id,
                        'url'  => $m->getUrl('thumb') ?: $m->getUrl(),
                        'name' => $m->file_name,
                    ])->toArray()
                    : [];
            } else {
                $mediaFolders = Product::query()
                    ->whereHas('media', fn ($q) => $q->where('collection_name', 'gallery'))
                    ->when($this->product?->id, fn ($q, $id) => $q->where('id', '!=', $id))
                    ->with(['media' => fn ($q) => $q->where('collection_name', 'gallery')->orderBy('order_column')->limit(1)])
                    ->withCount(['media' => fn ($q) => $q->where('collection_name', 'gallery')])
                    ->orderBy('sku')
                    ->get()
                    ->map(fn ($p) => [
                        'id'    => $p->id,
                        'name'  => $p->sku ?: $p->getTranslation('name', 'en'),
                        'count' => $p->media_count,
                        'cover' => $p->media->first()?->getUrl('thumb') ?: $p->media->first()?->getUrl(),
                    ])->toArray();
            }
        }

        return view('livewire.admin.products.form', compact('categories', 'mediaFolders', 'folderMedia'))
            ->layout('layouts.admin');
    }

    private function loadExistingImages(): void
    {
        $this->existingImages = $this->product
            ->getMedia('gallery')
            ->map(fn($m) => ['id' => $m->id, 'url' => $m->getUrl(), 'name' => $m->name])
            ->toArray();
    }
}
