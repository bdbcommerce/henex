<?php

namespace App\Livewire\Admin\Products;

use App\Models\Attribute;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
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

    public array $selectedCategories = [];
    /** Only the attributes added to this product: [attribute_id => [attribute_value_id, ...]] */
    public array $selectedAttributes = [];
    public array $existingImages = [];
    public array $newImages = [];

    public bool $showMediaBrowser = false;
    public ?int $browsingProductId = null;
    public bool $browsingUnused = false;
    public array $pickedMedia = [];
    /** Unused server files picked for re-attaching: [md5(path) => ['path', 'url', 'name']] */
    public array $pickedFiles = [];

    /** Disk folder every media file lives in (see App\Support\MediaLibrary\FlatPathGenerator). */
    private const MEDIA_DIR = 'images';

    public function mount(?Product $product = null): void
    {
        if ($product?->exists) {
            $this->product = $product;
            $this->form = array_merge($this->form, $product->only(['sku', 'slug', 'is_active', 'is_featured', 'sort_order']));
            foreach (['name', 'short_description', 'description', 'meta_title', 'meta_description'] as $field) {
                $this->form[$field] = $product->getTranslations($field);
            }
            $this->selectedCategories = $product->categories->pluck('id')->toArray();
            foreach ($product->attributeValues as $value) {
                $this->selectedAttributes[$value->attribute_id][] = $value->id;
            }
            $this->loadExistingImages();
        }
    }

    // ── Attributes ──────────────────────────────────────────────────────────

    public function addAttribute(int $id): void
    {
        if (! Attribute::active()->whereKey($id)->exists()) {
            return;
        }
        $this->selectedAttributes[$id] ??= [];
        $this->dispatch('attribute-added', id: $id);
    }

    public function addAllAttributes(): void
    {
        foreach (Attribute::active()->pluck('id') as $id) {
            $this->selectedAttributes[$id] ??= [];
        }
    }

    public function removeAttribute(int $attributeId): void
    {
        unset($this->selectedAttributes[$attributeId]);
    }

    // ── Images ──────────────────────────────────────────────────────────────

    /**
     * Remove an image from this product. With $deleteFile the file (and its thumb/medium
     * versions) is deleted from the server; otherwise it stays and appears under
     * "Unused files on server" in the media library for re-use.
     */
    public function deleteImage(int $mediaId, bool $deleteFile = true): void
    {
        $media = $this->product?->media()->where('collection_name', 'gallery')->whereKey($mediaId)->first();
        if (! $media) {
            return;
        }

        // Never delete a file another media record still points to.
        $shared = Media::where('id', '!=', $media->id)->where('disk', $media->disk)->where('file_name', $media->file_name)->exists();

        if ($deleteFile && ! $shared) {
            $media->delete();                // MediaLibrary removes the file + conversions
            $message = 'Image deleted from the product and the server.';
        } else {
            $media->deleteQuietly();         // record only — files stay on disk
            $message = $shared ? 'Image removed. The file is used by another product, so it was kept.' : 'Image removed from the product. The file was kept on the server.';
        }

        $this->loadExistingImages();
        $this->dispatch('toast', message: $message, type: 'success');
    }

    public function openMediaBrowser(): void
    {
        $this->browsingProductId = null;
        $this->browsingUnused = false;
        $this->showMediaBrowser = true;
    }

    public function closeMediaBrowser(): void
    {
        $this->showMediaBrowser = false;
        $this->browsingProductId = null;
        $this->browsingUnused = false;
    }

    public function browseFolder(?int $productId): void
    {
        $this->browsingProductId = $productId;
        $this->browsingUnused = false;
    }

    public function browseUnused(): void
    {
        $this->browsingProductId = null;
        $this->browsingUnused = true;
    }

    public function togglePickFile(string $path): void
    {
        $key = md5($path);
        if (isset($this->pickedFiles[$key])) {
            unset($this->pickedFiles[$key]);
            return;
        }
        if (! in_array($path, $this->unusedFiles(), true)) {
            return;
        }
        $this->pickedFiles[$key] = ['path' => $path, 'url' => Storage::disk('public')->url($path), 'name' => basename($path)];
    }

    public function removePickedFile(string $key): void
    {
        unset($this->pickedFiles[$key]);
    }

    /** Permanently delete an unused file (and its leftover thumb/medium versions) from the server. */
    public function deleteUnusedFile(string $path): void
    {
        if (! in_array($path, $this->unusedFiles(), true)) {
            return; // only files no media record uses
        }
        Storage::disk('public')->delete([$path, ...$this->conversionPaths($path)]);
        unset($this->pickedFiles[md5($path)]);
        $this->dispatch('toast', message: 'File deleted from the server.', type: 'success');
    }

    /**
     * Original image files in the media folder that no media record references
     * (conversion files like "x-thumb.webp" are skipped).
     *
     * @return string[] disk-relative paths
     */
    private function unusedFiles(): array
    {
        $used = Media::where('disk', 'public')->pluck('file_name')
            ->flatMap(fn ($name) => [$name, ...array_map('basename', $this->conversionPaths(self::MEDIA_DIR . '/' . $name))])
            ->flip();

        return collect(Storage::disk('public')->files(self::MEDIA_DIR))
            ->filter(fn ($path) => preg_match('/\.(jpe?g|png|webp|gif)$/i', $path))
            ->reject(fn ($path) => preg_match('/-(thumb|medium)\.webp$/i', $path))
            ->reject(fn ($path) => isset($used[basename($path)]))
            ->values()->all();
    }

    /** Paths of the generated thumb/medium versions that belong to an original file. */
    private function conversionPaths(string $path): array
    {
        $base = self::MEDIA_DIR . '/' . pathinfo($path, PATHINFO_FILENAME);

        return ["{$base}-thumb.webp", "{$base}-medium.webp"];
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
            'form.sku'     => 'nullable|unique:products,sku' . ($productId ? ",{$productId}" : ''),
            'newImages.*'  => 'nullable|image|max:4096',
        ], [
            'form.name.uz.required' => 'Product name (Uzbek) is required.',
            'form.sku.unique'       => 'This SKU is already in use.',
        ]);

        $this->product ??= new Product();
        $this->product->fill([
            'sku'         => $this->form['sku'] ?: null,
            'is_active'   => $this->form['is_active'],
            'is_featured' => $this->form['is_featured'],
            'sort_order'  => $this->form['sort_order'],
        ]);
        foreach (['name', 'short_description', 'description', 'meta_title', 'meta_description'] as $field) {
            $this->product->setTranslations($field, $this->form[$field]);
        }
        $this->product->save();

        $this->product->categories()->sync($this->selectedCategories);

        // Keep only value ids that really belong to the attribute they were picked under.
        $valueIds = Attribute::with('values:id,attribute_id')->get()
            ->flatMap(function ($attr) {
                $ids = array_values(array_intersect(
                    array_map('intval', $this->selectedAttributes[$attr->id] ?? []),
                    $attr->values->pluck('id')->all(),
                ));

                return $attr->is_multiple ? $ids : array_slice($ids, 0, 1);
            })->all();
        $this->product->attributeValues()->sync($valueIds);
        cache()->forget('bot_catalog_context');

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

        // Re-attach kept server files: the file is moved into the media record under a new
        // (unique) name — never onto its own path — and its stale thumb/medium files are removed.
        $unused = $this->unusedFiles();
        foreach ($this->pickedFiles as $file) {
            if (! in_array($file['path'], $unused, true)) {
                continue;
            }
            $this->product
                ->addMedia(Storage::disk('public')->path($file['path']))
                ->usingFileName(time() . '_' . preg_replace('/^\d{9,}_/', '', basename($file['path'])))
                ->toMediaCollection('gallery');
            Storage::disk('public')->delete($this->conversionPaths($file['path']));
        }
        $this->pickedFiles = [];

        session()->flash('success', 'Product saved successfully.');
        $this->redirect(route('admin.products.index'), navigate: false);
    }

    public function render()
    {
        $categories = Category::where('is_active', true)->whereNull('parent_id')->with('children')->get();
        $productAttributes = Attribute::active()->ordered()->with('values')->get();

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

        $unusedFiles = $this->showMediaBrowser
            ? collect($this->unusedFiles())->map(fn ($path) => [
                'path' => $path,
                'key'  => md5($path),
                'url'  => Storage::disk('public')->url($path),
                'name' => basename($path),
            ])->all()
            : [];

        return view('livewire.admin.products.form', compact('categories', 'productAttributes', 'mediaFolders', 'folderMedia', 'unusedFiles'))
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
