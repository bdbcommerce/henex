# Enterprise CMS Admin Panel

Professional admin interface for Henex.uz built with Livewire 3 and Tailwind CSS.

## Features

✅ **Professional Dashboard** — Stats cards, recent activity, quick links  
✅ **CRUD Operations** — Create, read, update, delete for all resources  
✅ **Search & Filtering** — Live search with debounce, status filters, sorting  
✅ **Responsive Design** — Works on desktop, tablet, mobile  
✅ **Toast Notifications** — Auto-dismiss success/error messages  
✅ **Confirmation Modals** — Prevent accidental deletions  
✅ **Role-Based Access** — Super admin vs editor permissions  
✅ **Multi-Locale Support** — Manage content in uz/ru/en  

---

## Access the Admin Panel

**URL:** `http://127.0.0.1:8000/admin`

Requires authentication. Create an account and assign the `super_admin` or `editor` role.

### User Roles

| Role | Permissions |
|------|-------------|
| **super_admin** | Full access: products, categories, articles, resellers, settings, users |
| **editor** | Content only: products, categories, articles, resellers (NO settings or users) |

---

## Architecture Overview

### Directory Structure

```
app/Livewire/Admin/
├── Dashboard.php                    # Main stats dashboard
├── Products/
│   ├── ProductIndex.php             # List view with filters
│   └── ProductForm.php              # Create/edit form
├── Categories/
│   ├── CategoryIndex.php
│   └── CategoryForm.php
├── Resellers/
│   ├── ResellerIndex.php
│   └── ResellerForm.php
├── Articles/
│   ├── ArticleIndex.php
│   └── ArticleForm.php
├── Slides/
│   └── SlideIndex.php               # Drag-to-reorder list
├── Inquiries/
│   └── InquiryIndex.php             # Read-only list
├── Settings/
│   └── SettingsForm.php             # Key-value editor
└── Users/
    └── UserIndex.php                # Admin users only
```

---

## Building CRUD Pages

### 1. Index (List) Component

**Pattern:** Search + filters + sortable table + pagination

```php
// app/Livewire/Admin/Products/ProductIndex.php
use Livewire\WithPagination;

class ProductIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';  // 'active', 'inactive', ''
    public string $sortBy = 'newest';  // 'newest', 'oldest', 'featured'
    public int $perPage = 20;

    protected $queryString = ['search', 'status', 'sortBy'];  // Sync to URL

    public function updatingSearch(): void {
        $this->resetPage();  // Reset to page 1 on search
    }

    public function delete(int $id): void {
        Product::findOrFail($id)->delete();
        $this->dispatch('toast', 
            message: 'Product deleted successfully.', 
            type: 'success'
        );
    }

    public function render() {
        $query = Product::query();

        // Apply filters
        if ($this->search) {
            $query->where('slug', 'like', "%{$this->search}%")
                  ->orWhere('sku', 'like', "%{$this->search}%");
        }
        if ($this->status) {
            $query->where('is_active', $this->status === 'active');
        }

        // Apply sort
        $query = match($this->sortBy) {
            'oldest' => $query->orderBy('created_at'),
            'featured' => $query->where('is_featured', true)->orderByDesc('created_at'),
            default => $query->orderByDesc('created_at'),
        };

        $products = $query->paginate($this->perPage);
        return view('livewire.admin.products.index', compact('products'))
            ->layout('layouts.admin');
    }
}
```

**View Template:**

```blade
<div x-data="{ deleteId: null }">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-neutral-950">Products</h1>
        <a href="{{ route('admin.products.create') }}" 
           class="bg-brand hover:bg-brand-dark text-white px-6 py-2 rounded-lg">
            + Add New Product
        </a>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="text"
                   placeholder="Search..."
                   wire:model.live.debounce.300ms="search"
                   class="px-4 py-2 border border-gray-300 rounded-lg focus:border-brand">
            <select wire:model.live="status" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
            <select wire:model.live="sortBy" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="newest">Newest First</option>
                <option value="oldest">Oldest First</option>
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Name</th>
                    <th class="px-6 py-4 text-center text-sm font-semibold">Status</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($products as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $product->name }}</td>
                        <td class="px-6 py-4 text-center">
                            {{ $product->is_active ? '✅' : '❌' }}
                        </td>
                        <td class="px-6 py-4 text-right flex gap-2 justify-end">
                            <a href="{{ route('admin.products.edit', $product) }}"
                               class="px-3 py-1 text-xs rounded-lg bg-blue-100 text-blue-700">
                                Edit
                            </a>
                            <button @click="deleteId = {{ $product->id }}"
                                    class="px-3 py-1 text-xs rounded-lg bg-red-100 text-red-700">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center text-gray-500">
                            No products found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Delete Modal --}}
    <div x-show="deleteId" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl max-w-sm">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-neutral-950">Delete Product?</h3>
            </div>
            <div class="px-6 py-4">
                <p class="text-gray-600">This will permanently delete the product.</p>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex gap-3 justify-end">
                <button @click="deleteId = null" class="px-4 py-2 hover:bg-gray-100 rounded">Cancel</button>
                <button @click="wire.call('delete', deleteId); deleteId = null"
                        class="px-4 py-2 bg-red-600 text-white rounded">Delete</button>
            </div>
        </div>
    </div>
</div>
```

---

### 2. Create/Edit Form Component

**Pattern:** Multi-locale tabs + translatable fields + media upload

```php
// app/Livewire/Admin/Products/ProductForm.php
use Livewire\WithFileUploads;

class ProductForm extends Component
{
    use WithFileUploads;

    public ?Product $product = null;
    public array $form = [
        'name' => ['uz' => '', 'ru' => '', 'en' => ''],
        'slug' => '',
        'sku' => '',
        'short_description' => ['uz' => '', 'ru' => '', 'en' => ''],
        'description' => ['uz' => '', 'ru' => '', 'en' => ''],
        'is_active' => true,
        'is_featured' => false,
    ];
    public array $newImages = [];
    public array $categories = [];

    public function mount(?Product $product = null): void {
        if ($product) {
            $this->product = $product;
            $this->form = [
                'name' => $product->getTranslations('name'),
                'slug' => $product->slug,
                'sku' => $product->sku,
                'short_description' => $product->getTranslations('short_description'),
                'description' => $product->getTranslations('description'),
                'is_active' => $product->is_active,
                'is_featured' => $product->is_featured,
            ];
            $this->categories = $product->categories->pluck('id')->toArray();
        }
    }

    public function save(): void {
        $this->validate([
            'form.name.uz' => 'required|min:3',
            'form.slug' => 'required|unique:products,slug' . ($this->product?->id ? ',' . $this->product->id : ''),
            'form.sku' => 'nullable|unique:products,sku' . ($this->product?->id ? ',' . $this->product->id : ''),
        ]);

        $product = $this->product ?: new Product();
        $product->fill($this->form);
        $product->save();

        $product->categories()->sync($this->categories);

        foreach ($this->newImages as $image) {
            $product->addMedia($image->getRealPath())
                ->usingFileName($image->getClientOriginalName())
                ->toMediaCollection('gallery');
        }

        $this->dispatch('toast', 
            message: $this->product ? 'Product updated.' : 'Product created.', 
            type: 'success'
        );

        redirect()->route('admin.products.index');
    }

    public function render() {
        return view('livewire.admin.products.form')
            ->layout('layouts.admin');
    }
}
```

**View Template (Excerpt):**

```blade
<form wire:submit="save">
    {{-- Locale Tabs --}}
    <div x-data="{ locale: 'uz' }" class="mb-8">
        <div class="flex gap-2 mb-4 border-b border-gray-200">
            @foreach(['uz' => "O'zbek", 'ru' => 'Русский', 'en' => 'English'] as $code => $label)
                <button type="button"
                        @click="locale = '{{ $code }}'"
                        :class="locale === '{{ $code }}' ? 'border-b-2 border-brand text-brand font-semibold' : 'text-gray-600'"
                        class="pb-2 px-3">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- Name Field --}}
        @foreach(['uz', 'ru', 'en'] as $code)
            <div x-show="locale === '{{ $code }}'">
                <label class="block text-sm font-medium text-neutral-950 mb-2">
                    Product Name ({{ $code }})
                </label>
                <input type="text"
                       wire:model="form.name.{{ $code }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-brand focus:ring-1 focus:ring-brand">
                @error("form.name.{{ $code }}")
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        @endforeach
    </div>

    {{-- Image Upload --}}
    <div class="mb-8">
        <label class="block text-sm font-medium text-neutral-950 mb-2">
            Product Images
        </label>
        <input type="file"
               wire:model="newImages"
               multiple
               accept="image/*"
               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
        @if($newImages)
            <p class="text-xs text-gray-600 mt-2">{{ count($newImages) }} image(s) selected</p>
        @endif
    </div>

    {{-- Categories --}}
    <div class="mb-8">
        <label class="block text-sm font-medium text-neutral-950 mb-2">
            Categories
        </label>
        <select wire:model="categories" multiple class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            @foreach(Category::all() as $category)
                <option value="{{ $category->id }}">
                    {{ $category->getTranslation('name', app()->getLocale()) }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Buttons --}}
    <div class="flex gap-3">
        <button type="submit"
                class="bg-brand hover:bg-brand-dark text-white px-6 py-2 rounded-lg font-medium transition">
            Save Product
        </button>
        <a href="{{ route('admin.products.index') }}"
           class="px-6 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50">
            Cancel
        </a>
    </div>
</form>
```

---

## Styling Conventions

### Colors

```css
/* Brand Colors */
.text-brand         /* Orange: #FF8C42 */
.bg-brand
.hover:bg-brand-dark  /* Darker orange: #D35400 */

/* Status Colors */
.bg-green-100      /* Active/Success */
.bg-red-100        /* Error/Delete */
.bg-blue-100       /* Info/Edit */
.bg-yellow-100     /* Warning */
.bg-gray-100       /* Neutral/Disabled */
```

### Components

```blade
{{-- Button Styles --}}
<button class="bg-brand hover:bg-brand-dark text-white px-4 py-2 rounded-lg font-medium transition">
    Primary
</button>
<button class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50">
    Secondary
</button>
<button class="px-3 py-1 text-xs rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200">
    Small / Icon
</button>

{{-- Input Styles --}}
<input class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-brand focus:ring-1 focus:ring-brand">

{{-- Status Badge --}}
<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Active</span>
```

---

## Features by Resource

### Products
- ✅ Search by name/SKU
- ✅ Filter by status (active/inactive) and featured
- ✅ Multi-locale product names & descriptions
- ✅ Gallery image upload (MediaLibrary)
- ✅ Category assignment (pivot)
- ✅ Specifications (repeating fields)

### Categories
- ✅ Hierarchical (parent/child)
- ✅ Featured icon/image
- ✅ Drag-to-reorder sort

### Resellers
- ✅ Region assignment (14 Uzbek regions)
- ✅ Type filter (reseller/service_center/both)
- ✅ Location map (Yandex Maps iframe)
- ✅ Contact info (phone, email, address)

### Articles
- ✅ Rich text editor (HTML)
- ✅ Publish/draft states
- ✅ Author assignment
- ✅ Type: news/blog/guide

### Inquiries
- ✅ Read-only list view
- ✅ Mark as read/unread
- ✅ Filter by product
- ✅ Response notes

---

## Toast Notifications

Dispatch toast messages from Livewire components:

```php
$this->dispatch('toast', 
    message: 'Product saved successfully.',
    type: 'success'  // 'success', 'error', 'info', 'warning'
);
```

Auto-dismisses after 3 seconds. Styled by type with icon and color.

---

## Best Practices

### ✅ DO

- Use `wire:model.live.debounce.300ms` for search fields
- Use `x-data` for modal state (Alpine.js)
- Validate on the server; display errors next to fields
- Use role-based middleware: `Route::middleware('role:super_admin')->group()`
- Cache seldom-changing data (Category, Region lists)
- Use translatable attributes (`getTranslations()`, `setTranslations()`)
- Dispatch toasts for user feedback

### ❌ DON'T

- Hardcode colors; use Tailwind utilities
- Use `wire:click="delete(...)" wire:confirm="..."` (janky); use Alpine modals instead
- Load relationships unnecessarily (use `select()` and `with()`)
- Cache Eloquent models with database cache driver (serialization issues)
- Mix blade template logic; keep views simple

---

## Common Tasks

### Add a new CRUD resource

1. Generate migration: `php artisan make:migration create_foos_table`
2. Create model: `php artisan make:model Foo`
3. Add relationships to model
4. Create Livewire components:
   - `app/Livewire/Admin/Foos/FooIndex.php`
   - `app/Livewire/Admin/Foos/FooForm.php`
5. Create views:
   - `resources/views/livewire/admin/foos/index.blade.php`
   - `resources/views/livewire/admin/foos/form.blade.php`
6. Add routes in `routes/web.php` (admin group)
7. Add sidebar nav link in `layouts/admin.blade.php`

### Add a search filter

In the component:

```php
public string $search = '';

protected $queryString = ['search'];  // Sync to URL

public function updatingSearch(): void {
    $this->resetPage();
}

public function render() {
    $query = Model::query();
    if ($this->search) {
        $query->where('name', 'like', "%{$this->search}%");
    }
    // ...
}
```

In the view:

```blade
<input wire:model.live.debounce.300ms="search" placeholder="Search...">
```

### Add a modal confirmation

Use Alpine.js `x-data`:

```blade
<div x-data="{ deleteId: null }">
    <button @click="deleteId = {{ $item->id }}" class="...">Delete</button>

    <div x-show="deleteId" class="fixed inset-0 bg-black/50 flex items-center justify-center">
        <div class="bg-white rounded-xl">
            <p>Delete this item?</p>
            <button @click="deleteId = null">Cancel</button>
            <button @click="wire.call('delete', deleteId); deleteId = null">Delete</button>
        </div>
    </div>
</div>
```

---

## Deployment Checklist

- [ ] All admin routes have `auth` + `role` middleware
- [ ] Delete actions require confirmation
- [ ] Search fields use `debounce.300ms` to avoid DB overload
- [ ] Images are resized & converted to WebP on upload
- [ ] Pagination defaults to sensible limits (20–50 items)
- [ ] No sensitive data logged in views
- [ ] Toast notifications for all user actions
- [ ] Mobile responsive (test on tablet/phone)

---

## Support

For issues or features, update CLAUDE.md with the spec change and rebuild relevant components.
