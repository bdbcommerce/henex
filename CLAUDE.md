# Henex.uz — Project Specification

Distributor website for **HENEX (Guangzhou) Technology Co., Ltd** barcode scanners in Uzbekistan.
Stack: **Laravel 11 · Livewire 3 · Tailwind CSS 3 · Alpine.js · MySQL 8**

---

## 1. Overview

| Item | Value |
|---|---|
| Domain | henex.uz |
| Audience | B2C end-users + B2B resellers |
| Languages | Uzbek (`uz`), Russian (`ru`), English (`en`) |
| Default locale | `uz` |
| Monetisation | No cart — "contact to buy" / reseller referral |
| CMS admin | Custom admin — Livewire 3 + Tailwind CSS |

### Business goals
1. Establish brand authority for Henex in Uzbekistan
2. Drive product discovery → hand off to regional resellers
3. SEO dominance for Uzbek barcode-scanner queries (uz + ru)
4. Provide a service-center locator to reduce support load

---

## 2. Tech Stack

```
Laravel 11
├── Livewire 3            # reactive frontend + admin components
├── Alpine.js 3           # UI micro-interactions (bundled with Livewire)
├── Tailwind CSS 3        # utility-first styling (frontend + admin)
└── Vite                  # asset bundler

MySQL 8                   # primary database

Key packages:
├── spatie/laravel-translatable          # JSON-column model translations
├── spatie/laravel-medialibrary          # product images / file uploads
├── spatie/laravel-permission            # admin roles (super_admin, editor)
├── spatie/laravel-sitemap               # auto-generated XML sitemap
├── spatie/laravel-sluggable             # auto slugs per locale
├── mcamara/laravel-localization         # locale URL prefix routing
├── artesaos/seotools                    # meta/OG/JSON-LD helpers
├── intervention/image-laravel           # image resize/webp conversion
└── livewire/sortable                    # drag-and-drop sort in admin
```

### Why custom admin over Filament?
- Full visual control — your own design system, no overrides
- Shared Tailwind config between frontend and admin (same brand colors)
- Livewire components are identical in structure to frontend — one mental model
- No opaque "magic" — every table, form, and action is a Blade + Livewire component you wrote

---

## 3. Database Schema

### 3.1 `regions` — Uzbekistan's 14 regions + Tashkent city
```sql
id          bigint PK
slug        varchar(60) UNIQUE          -- tashkent-city, andijan, ...
name        json                        -- {"uz":"Toshkent shahri","ru":"г. Ташкент","en":"Tashkent city"}
sort_order  tinyint DEFAULT 0
```

Seed all 14:
Toshkent shahri, Toshkent viloyati, Andijon, Farg'ona, Namangan,
Samarqand, Buxoro, Xorazm, Qashqadaryo, Surxondaryo, Jizzax, Sirdaryo, Navoiy, Qoraqalpog'iston

### 3.2 `categories`
```sql
id              bigint PK
slug            varchar(120) UNIQUE
parent_id       bigint FK nullable       -- for sub-categories
image           varchar(255) nullable    -- via MediaLibrary
sort_order      smallint DEFAULT 0
is_active       boolean DEFAULT true
name            json                     -- translatable
description     json                     -- translatable
meta_title      json                     -- translatable
meta_description json                   -- translatable
created_at / updated_at
```

Seed top-level categories from henex.cn:
- Desktop Barcode Scanners
- Wired Barcode Scanners
- Wireless Barcode Scanners
- Wearable Barcode Scanners
- Industrial Barcode Scanners
- Barcode Scanner Modules
- Data Collectors
- POS Terminals

### 3.3 `products`
```sql
id              bigint PK
sku             varchar(60) UNIQUE nullable
slug            varchar(180) UNIQUE
is_featured     boolean DEFAULT false
is_active       boolean DEFAULT true
sort_order      smallint DEFAULT 0
-- translatable JSON columns:
name            json
short_description json
description     json    -- full rich-text HTML
meta_title      json
meta_description json
created_at / updated_at
```

### 3.4 `category_product` (pivot)
```sql
category_id bigint FK
product_id  bigint FK
PRIMARY KEY (category_id, product_id)
```

### 3.5 `product_specifications`
```sql
id          bigint PK
product_id  bigint FK
sort_order  smallint DEFAULT 0
key         json    -- {"uz":"Interfeys","ru":"Интерфейс","en":"Interface"}
value       json    -- {"uz":"USB","ru":"USB","en":"USB"}
```
Rendered as a spec table on the product page.

### 3.6 `product_images` (handled by MediaLibrary)
MediaLibrary creates `media` table automatically.
Register two collections per product:
- `gallery`   — product photos (multiple, ordered)
- `documents` — datasheets / manuals (PDF)

### 3.7 `resellers`
```sql
id              bigint PK
region_id       bigint FK
name            varchar(200)
type            enum('reseller','service_center','both')
phone           varchar(50) nullable
phone2          varchar(50) nullable
email           varchar(120) nullable
website         varchar(255) nullable
address         json        -- translatable address string
latitude        decimal(10,7) nullable
longitude       decimal(10,7) nullable
is_active       boolean DEFAULT true
sort_order      smallint DEFAULT 0
created_at / updated_at
```

### 3.8 `articles`
```sql
id              bigint PK
slug            varchar(180) UNIQUE
author_id       bigint FK (users)
type            enum('news','blog','guide') DEFAULT 'news'
is_published    boolean DEFAULT false
published_at    timestamp nullable
cover_image     varchar(255) nullable   -- via MediaLibrary
title           json
excerpt         json
content         json    -- rich HTML
meta_title      json
meta_description json
created_at / updated_at
```

### 3.9 `slides` (homepage hero)
```sql
id          bigint PK
sort_order  smallint DEFAULT 0
is_active   boolean DEFAULT true
link        varchar(255) nullable
title       json
subtitle    json
button_text json
-- image via MediaLibrary 'slide' collection
```

### 3.10 `inquiries` (contact / quote requests)
```sql
id          bigint PK
name        varchar(120)
company     varchar(120) nullable
phone       varchar(50)
email       varchar(120) nullable
message     text
product_id  bigint FK nullable
locale      char(2)
is_read     boolean DEFAULT false
created_at / updated_at
```

### 3.11 `settings`
```sql
key     varchar(100) PK
value   text
```
Stores: site_phone, site_email, social_* links, address, working_hours, etc.

---

## 4. Models

```
App\Models\
├── Region              hasMany Resellers
├── Category            belongsToMany Products; hasMany children; belongsTo parent
├── Product             belongsToMany Categories; hasMany Specifications; HasMedia
├── ProductSpecification belongsTo Product
├── Reseller            belongsTo Region
├── Article             belongsTo User
├── Slide
├── Inquiry             belongsTo Product (nullable)
└── Setting             (key-value, use Settings::get('site_phone'))
```

All translatable models use `Spatie\Translatable\HasTranslations` trait.
All image models use `Spatie\MediaLibrary\HasMedia` interface.

### Settings helper (app/Helpers/Settings.php)
```php
class Settings {
    public static function get(string $key, $default = null): mixed {
        return cache()->remember("setting_{$key}", 3600,
            fn() => \App\Models\Setting::find($key)?->value ?? $default
        );
    }
    public static function set(string $key, $value): void {
        \App\Models\Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        cache()->forget("setting_{$key}");
    }
}
```

---

## 5. Routing

Use `mcamara/laravel-localization` for locale prefix routing.

```
/                       → redirect to /{default-locale}
/{locale}               → HomeController
/{locale}/products      → ProductCatalogController (Livewire page)
/{locale}/products/{slug} → ProductDetailController
/{locale}/where-to-buy  → ResellersController (Livewire page)
/{locale}/news          → ArticleListController
/{locale}/news/{slug}   → ArticleDetailController
/{locale}/contact       → ContactController (Livewire form)
/{locale}/about         → StaticPageController
/sitemap.xml            → auto-generated by spatie/laravel-sitemap
```

Admin routes (Filament):
```
/admin                  → Filament dashboard
/admin/products
/admin/categories
/admin/resellers
/admin/articles
/admin/slides
/admin/inquiries
/admin/settings
/admin/users
```

---

## 6. Frontend Pages & Livewire Components

### 6.1 Layouts

`resources/views/layouts/app.blade.php`
- Sticky header: logo | nav | locale switcher | phone CTA
- Hero area (homepage only)
- Footer: address, phone, social links, region links

### 6.2 Homepage sections (in order)
1. **HeroSlider** — Livewire component, auto-advance, Alpine.js transitions
2. **FeaturedCategories** — 8 category cards with icons
3. **FeaturedProducts** — `is_featured=true` products, horizontal scroll on mobile
4. **IndustryApplications** — static icon grid: Retail, Medical, Warehousing, Manufacturing, etc.
5. **WhyHenex** — 4 trust pillars: Official Distributor · Warranty Service · Technical Support · Fast Delivery
6. **ResellerCTA** — "Find in your region" → `/where-to-buy`
7. **LatestNews** — last 3 articles
8. **ContactStrip** — phone + email + quick inquiry form

### 6.3 Product Catalog — `ProductCatalog` Livewire component
Props/state:
- `$categorySlug` — from URL
- `$search` — live search (debounce 300ms)
- `$sort` — name_asc / name_desc / newest
- `$page` — pagination

Features:
- Left sidebar: category tree filter
- Grid / list view toggle (Alpine)
- Product card: image, name (locale), SKU, "Details" button, "Request Quote" button
- URL-synced filters (Livewire `#[Url]` attribute)
- SEO: canonical + hreflang on every filter state

### 6.4 Product Detail page
- Image gallery (Alpine lightbox or Swiper.js CDN)
- Name, short description
- Specs table (from `product_specifications`)
- Documents / datasheet download (PDF)
- **"Request a Quote"** Livewire modal form → saves to `inquiries`
- **"Find where to buy"** → anchor link to reseller section or `/where-to-buy`
- Breadcrumb: Home → Category → Product
- JSON-LD `Product` schema

### 6.5 Where to Buy — `ResellerFinder` Livewire component
State:
- `$regionSlug` — from URL or selected
- `$type` — all / reseller / service_center

UI:
- Region tabs (horizontal scroll on mobile) — all 14 regions
- Cards per reseller: name, type badge, phone, address, website link, map link
- Optional: embed Yandex Maps (widely used in Uzbekistan) iframe per region group

No Google Maps API needed — Yandex Maps is dominant in UZ, free iframe embed works fine.

### 6.6 Contact — `ContactForm` Livewire component
Fields: Name*, Company, Phone*, Email, Product (optional select), Message
Validation: Livewire real-time
On submit: save to `inquiries`, send mail to admin (queue)

---

## 7. Admin Panel (Custom Livewire + Tailwind)

### Architecture

All admin routes live under `/admin/*` and are protected by the `auth` + `role:super_admin,editor` middleware.
Admin views use a dedicated layout `resources/views/layouts/admin.blade.php` with a sidebar nav, separate from the public site layout.

```
/admin                   → AdminDashboard (stats: products, inquiries, articles)
/admin/products          → Admin\ProductIndex  (table + search + filters)
/admin/products/create   → Admin\ProductForm
/admin/products/{id}     → Admin\ProductForm   (edit mode)
/admin/categories        → Admin\CategoryIndex
/admin/categories/create → Admin\CategoryForm
/admin/resellers         → Admin\ResellerIndex
/admin/resellers/create  → Admin\ResellerForm
/admin/articles          → Admin\ArticleIndex
/admin/articles/create   → Admin\ArticleForm
/admin/slides            → Admin\SlideIndex    (with drag-to-reorder)
/admin/inquiries         → Admin\InquiryIndex  (read-only, mark-as-read)
/admin/settings          → Admin\SettingsForm  (single-page key-value)
/admin/users             → Admin\UserIndex     (super_admin only)
```

### Admin layout (`layouts/admin.blade.php`)
```
┌──────────────────────────────────────────────────────┐
│  HENEX Admin  [sidebar toggle]          [user menu]  │  ← top bar
├─────────────┬────────────────────────────────────────┤
│  Dashboard  │                                        │
│  Products   │   <main content area>                  │
│  Categories │   Livewire component renders here      │
│  Resellers  │                                        │
│  Articles   │                                        │
│  Slides     │                                        │
│  Inquiries  │                                        │
│  Settings   │                                        │
│  Users      │                                        │
└─────────────┴────────────────────────────────────────┘
```

Sidebar is a Blade include. Active state via `request()->routeIs('admin.products.*')`.
Collapsible on mobile via Alpine.js `x-data="{ open: false }"`.

### Livewire admin components

Each entity has two components:
- `Index` — paginated data table with search, filters, bulk delete, sort
- `Form` — create/edit form with real-time validation

```
app/Livewire/Admin/
├── Dashboard.php
├── Products/
│   ├── ProductIndex.php     # $search, $perPage, $sortField, $sortDir
│   └── ProductForm.php      # $product, $locale tabs, $specs (array), image upload
├── Categories/
│   ├── CategoryIndex.php
│   └── CategoryForm.php
├── Resellers/
│   ├── ResellerIndex.php
│   └── ResellerForm.php
├── Articles/
│   ├── ArticleIndex.php
│   └── ArticleForm.php     # rich-text editor (TipTap or Quill via CDN)
├── Slides/
│   └── SlideIndex.php      # sortable list (livewire/sortable)
├── Inquiries/
│   └── InquiryIndex.php    # read-only, markAsRead action
├── Settings/
│   └── SettingsForm.php
└── Users/
    └── UserIndex.php
```

### Translatable field pattern in admin forms

Since we use `spatie/laravel-translatable`, admin forms show locale tabs built manually:

```blade
{{-- resources/views/livewire/admin/partials/locale-tabs.blade.php --}}
<div x-data="{ locale: 'uz' }">
    {{-- Tab bar --}}
    <div class="flex gap-2 mb-4 border-b border-gray-200">
        @foreach(['uz' => "O'zbek", 'ru' => 'Русский', 'en' => 'English'] as $code => $label)
            <button type="button"
                    @click="locale = '{{ $code }}'"
                    :class="locale === '{{ $code }}' ? 'border-b-2 border-brand text-brand font-semibold' : 'text-gray-500'"
                    class="pb-2 px-3 text-sm transition">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Name field --}}
    @foreach(['uz','ru','en'] as $code)
        <div x-show="locale === '{{ $code }}'">
            <input type="text"
                   wire:model="form.name.{{ $code }}"
                   placeholder="Name ({{ $code }})"
                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand focus:ring-brand">
        </div>
    @endforeach
</div>
```

`ProductForm.php` binds `$form->name` as an array: `['uz' => '', 'ru' => '', 'en' => '']`.
On save: `$product->setTranslations('name', $this->form->name)`.

### Product specs repeater (pure Livewire)

```php
// In ProductForm.php
public array $specs = []; // [['key' => ['uz'=>'','ru'=>'','en'=>''], 'value' => [...], 'sort_order' => 0]]

public function addSpec(): void
{
    $this->specs[] = ['key' => ['uz'=>'','ru'=>'','en'=>''], 'value' => ['uz'=>'','ru'=>'','en'=>''], 'sort_order' => count($this->specs)];
}

public function removeSpec(int $index): void
{
    array_splice($this->specs, $index, 1);
}
```

Rendered as a sortable list in the view with "Add row" / "Remove" buttons.

### Image upload (MediaLibrary + Livewire)

```php
// In ProductForm.php
use Livewire\WithFileUploads;

public array $newImages = [];    // temporary uploads
public array $existingImages = []; // already saved media

public function uploadImages(): void
{
    foreach ($this->newImages as $file) {
        $this->product->addMedia($file->getRealPath())
            ->usingFileName($file->getClientOriginalName())
            ->toMediaCollection('gallery');
    }
    $this->newImages = [];
    $this->loadExistingImages();
}

public function deleteImage(int $mediaId): void
{
    $this->product->getMedia('gallery')->find($mediaId)?->delete();
    $this->loadExistingImages();
}
```

Image preview grid shown below the upload input. WebP conversion runs in an `uploaded` observer on the Media model.

### Admin roles (spatie/laravel-permission)

```php
// Two roles only:
// super_admin — everything
// editor      — products, categories, articles, resellers (NO users, NO settings)

// Middleware on route group:
Route::middleware(['auth', 'role:super_admin,editor'])->prefix('admin')->group(function () {
    // shared routes
});
Route::middleware(['auth', 'role:super_admin'])->prefix('admin')->group(function () {
    Route::livewire('/users', Admin\Users\UserIndex::class)->name('admin.users');
    Route::livewire('/settings', Admin\Settings\SettingsForm::class)->name('admin.settings');
});
```

### Admin UI design conventions
- Background: `bg-gray-50` page, `bg-white` cards with `shadow-sm rounded-xl`
- Primary action buttons: `bg-brand text-white hover:bg-brand-dark`
- Table rows: `hover:bg-gray-50`, alternating subtle stripe via `even:bg-gray-25`
- Search input: always top-left of table, `wire:model.live.debounce.300ms="search"`
- Pagination: Livewire built-in `WithPagination`, simple prev/next + page links
- Flash messages: Alpine-driven toast, auto-dismiss after 3 s
- Delete: always a confirmation modal (`x-data="{ confirmId: null }"`) before action

---

## 8. SEO Strategy

### 8.1 URL structure
```
/uz/mahsulotlar/simsiz-shtrix-kod-skanerlari/h500
/ru/produkty/besprovodnye-skanery-shtrih-koda/h500
/en/products/wireless-barcode-scanners/h500
```
Each locale gets its own translated slug. Store slugs per locale in a `slugs` JSON column or use a dedicated `product_slugs` table keyed by (product_id, locale).

### 8.2 Hreflang
In every `<head>`:
```html
<link rel="alternate" hreflang="uz" href="https://henex.uz/uz/mahsulotlar/..." />
<link rel="alternate" hreflang="ru" href="https://henex.uz/ru/produkty/..." />
<link rel="alternate" hreflang="en" href="https://henex.uz/en/products/..." />
<link rel="alternate" hreflang="x-default" href="https://henex.uz/uz/mahsulotlar/..." />
```
Use `mcamara/laravel-localization` `getLocalizedURL()` helper in layout.

### 8.3 Meta tags per page
Use `artesaos/seotools`:
```php
SEOMeta::setTitle($product->getTranslation('meta_title', app()->getLocale()));
SEOMeta::setDescription($product->getTranslation('meta_description', app()->getLocale()));
OpenGraph::setUrl(request()->url());
OpenGraph::addImage($product->getFirstMediaUrl('gallery'));
```
Fallback: if meta_title empty → use product name + " | Henex Uzbekistan"

### 8.4 JSON-LD structured data
Product pages → `Product` schema:
```json
{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": "...",
  "image": ["..."],
  "description": "...",
  "sku": "H500",
  "brand": { "@type": "Brand", "name": "HENEX" },
  "offers": {
    "@type": "Offer",
    "availability": "https://schema.org/InStoreOnly",
    "seller": { "@type": "Organization", "name": "Henex Uzbekistan" }
  }
}
```

Homepage → `Organization` schema with address, phone, social profiles.

### 8.5 Sitemap
```php
// routes/web.php (or scheduled command)
Sitemap::create()
    ->add(Url::create('/uz')->setPriority(1.0))
    ->add(Url::create('/ru')->setPriority(1.0))
    ->add(... all product/category/article URLs for each locale ...)
    ->writeToFile(public_path('sitemap.xml'));
```
Schedule: `php artisan sitemap:generate` daily.

### 8.6 Performance (Core Web Vitals)
- All product images converted to **WebP** on upload (Intervention Image)
- Responsive srcset: 400w, 800w, 1200w
- Tailwind CSS purged in production (zero unused classes)
- Alpine.js and Livewire scripts deferred
- HTTP/2 + gzip on nginx
- Redis for session + cache in production

### 8.7 Target keywords (uz + ru)
| Locale | Primary keywords |
|---|---|
| ru | штрих-код сканер Ташкент, купить сканер штрих-кода Узбекистан, Henex сканер |
| uz | shtrix-kod skaneri Toshkent, Henex Oʻzbekiston, barkod skaneri |
| en | barcode scanner Uzbekistan, Henex Uzbekistan distributor |

---

## 9. Multilingual Implementation

### Locale detection & switching
```php
// config/laravellocalization.php
'supportedLocales' => [
    'uz' => ['name' => "O'zbek", 'script' => 'Latn', 'regional' => 'uz_UZ'],
    'ru' => ['name' => 'Русский', 'script' => 'Cyrl', 'regional' => 'ru_RU'],
    'en' => ['name' => 'English', 'script' => 'Latn', 'regional' => 'en_US'],
],
'hideDefaultLocaleInURL' => false,  // always show /uz/, /ru/, /en/
```

### UI string translations
`lang/uz/site.php`, `lang/ru/site.php`, `lang/en/site.php`
Keys: nav.products, nav.where_to_buy, nav.news, nav.about, nav.contact,
      cta.request_quote, cta.find_reseller, form.name, form.phone, ...

### Model translations (spatie/laravel-translatable)
```php
class Product extends Model {
    use HasTranslations;
    public array $translatable = ['name','short_description','description','meta_title','meta_description'];
}
// Usage:
$product->name              // current app locale
$product->getTranslation('name', 'ru')
$product->setTranslation('name', 'uz', 'Simsiz skaner')
```

---

## 10. Locale-Aware Tailwind Theming

### Typography
- Uzbek Latin + English: `font-sans` (Inter or Nunito)
- Russian Cyrillic: same Inter (excellent Cyrillic support)
- Set `lang` attribute on `<html>` dynamically per locale

### RTL
No RTL needed (Uzbek, Russian, English are all LTR).

### Tailwind config additions (CSS v4 @theme syntax)
```js
// resources/css/app.css
@theme {
    --font-sans: 'Inter', ui-sans-serif, system-ui, sans-serif;
    --color-brand: #FF8C42;        // HENEX Orange (primary brand color)
    --color-brand-dark: #D35400;   // Dark orange
    --color-brand-light: #FFB366;  // Light orange
    --color-neutral-950: #0A0A0A;  // Almost black
}
```

---

## 11. Project File Structure

```
henex-uz/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── HomeController.php
│   │   │   ├── ProductController.php
│   │   │   ├── ArticleController.php
│   │   │   └── ResellerController.php
│   │   └── Middleware/
│   │       └── SetLocale.php
│   ├── Livewire/
│   │   ├── Frontend/                    # public-facing components
│   │   │   ├── HeroSlider.php
│   │   │   ├── ProductCatalog.php
│   │   │   ├── ProductQuoteForm.php
│   │   │   ├── ResellerFinder.php
│   │   │   └── ContactForm.php
│   │   └── Admin/                       # admin panel components
│   │       ├── Dashboard.php
│   │       ├── Products/
│   │       │   ├── ProductIndex.php
│   │       │   └── ProductForm.php
│   │       ├── Categories/
│   │       │   ├── CategoryIndex.php
│   │       │   └── CategoryForm.php
│   │       ├── Resellers/
│   │       │   ├── ResellerIndex.php
│   │       │   └── ResellerForm.php
│   │       ├── Articles/
│   │       │   ├── ArticleIndex.php
│   │       │   └── ArticleForm.php
│   │       ├── Slides/
│   │       │   └── SlideIndex.php
│   │       ├── Inquiries/
│   │       │   └── InquiryIndex.php
│   │       ├── Settings/
│   │       │   └── SettingsForm.php
│   │       └── Users/
│   │           └── UserIndex.php
│   ├── Models/
│   │   ├── Category.php
│   │   ├── Product.php
│   │   ├── ProductSpecification.php
│   │   ├── Region.php
│   │   ├── Reseller.php
│   │   ├── Article.php
│   │   ├── Slide.php
│   │   ├── Inquiry.php
│   │   └── Setting.php
│   └── Helpers/
│       └── Settings.php
├── database/
│   ├── migrations/
│   │   ├── create_regions_table.php
│   │   ├── create_categories_table.php
│   │   ├── create_products_table.php
│   │   ├── create_category_product_table.php
│   │   ├── create_product_specifications_table.php
│   │   ├── create_resellers_table.php
│   │   ├── create_articles_table.php
│   │   ├── create_slides_table.php
│   │   ├── create_inquiries_table.php
│   │   └── create_settings_table.php
│   └── seeders/
│       ├── RegionSeeder.php             -- all 14 UZ regions
│       └── CategorySeeder.php           -- 8 Henex product categories
├── resources/views/
│   ├── layouts/
│   │   ├── app.blade.php                -- public site layout
│   │   └── admin.blade.php             -- admin layout (sidebar + topbar)
│   ├── pages/                           -- public pages (rendered by controllers)
│   │   ├── home.blade.php
│   │   ├── products/
│   │   │   ├── index.blade.php
│   │   │   └── show.blade.php
│   │   ├── resellers.blade.php
│   │   ├── news/
│   │   │   ├── index.blade.php
│   │   │   └── show.blade.php
│   │   ├── contact.blade.php
│   │   └── about.blade.php
│   ├── livewire/
│   │   ├── frontend/                    -- public Livewire views
│   │   │   ├── hero-slider.blade.php
│   │   │   ├── product-catalog.blade.php
│   │   │   ├── product-quote-form.blade.php
│   │   │   ├── reseller-finder.blade.php
│   │   │   └── contact-form.blade.php
│   │   └── admin/                       -- admin Livewire views
│   │       ├── dashboard.blade.php
│   │       ├── products/
│   │       │   ├── index.blade.php
│   │       │   └── form.blade.php
│   │       ├── categories/
│   │       │   ├── index.blade.php
│   │       │   └── form.blade.php
│   │       ├── resellers/
│   │       │   ├── index.blade.php
│   │       │   └── form.blade.php
│   │       ├── articles/
│   │       │   ├── index.blade.php
│   │       │   └── form.blade.php
│   │       ├── slides/
│   │       │   └── index.blade.php
│   │       ├── inquiries/
│   │       │   └── index.blade.php
│   │       ├── settings/
│   │       │   └── form.blade.php
│   │       └── partials/
│   │           ├── locale-tabs.blade.php   -- reusable uz/ru/en tab switcher
│   │           ├── image-uploader.blade.php
│   │           └── confirm-modal.blade.php
│   └── components/
│       ├── admin/
│       │   ├── sidebar.blade.php
│       │   ├── topbar.blade.php
│       │   ├── stats-card.blade.php
│       │   └── table-action-buttons.blade.php
│       └── site/
│           ├── product-card.blade.php
│           ├── breadcrumb.blade.php
│           └── locale-switcher.blade.php
├── lang/
│   ├── uz/site.php
│   ├── ru/site.php
│   └── en/site.php
├── routes/
│   ├── web.php                          -- public + admin routes
│   └── admin.php                        -- included in web.php under /admin prefix
└── public/
    └── sitemap.xml                      -- generated by scheduler
```

---

## 12. Installation Commands

```bash
# Create project
composer create-project laravel/laravel henex-uz
cd henex-uz

# Core packages
composer require livewire/livewire
composer require livewire/sortable                    # drag-to-reorder slides, specs
composer require spatie/laravel-translatable
composer require spatie/laravel-medialibrary
composer require spatie/laravel-permission
composer require spatie/laravel-sitemap
composer require spatie/laravel-sluggable
composer require mcamara/laravel-localization
composer require artesaos/seotools
composer require intervention/image-laravel

# Publish config & migrations
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-migrations"
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Mcamara\LaravelLocalization\LaravelLocalizationServiceProvider"
php artisan migrate
php artisan db:seed

# Tailwind + frontend libs
npm install -D tailwindcss @tailwindcss/typography @tailwindcss/forms
npm install swiper
npm run build
```

### tailwind.config.js — content paths
```js
export default {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    // ... theme extensions from section 10
}
```

### Livewire config note
Alpine.js ships bundled with Livewire 3 — do NOT `npm install alpinejs` separately or you get two Alpine instances. Use `window.Alpine` from Livewire's bundle.

### Admin route file (`routes/admin.php`)
```php
use App\Livewire\Admin;

Route::middleware(['auth', 'role:super_admin,editor'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', Admin\Dashboard::class)->name('dashboard');
    Route::get('/products', Admin\Products\ProductIndex::class)->name('products.index');
    Route::get('/products/create', Admin\Products\ProductForm::class)->name('products.create');
    Route::get('/products/{product}/edit', Admin\Products\ProductForm::class)->name('products.edit');
    Route::get('/categories', Admin\Categories\CategoryIndex::class)->name('categories.index');
    Route::get('/categories/create', Admin\Categories\CategoryForm::class)->name('categories.create');
    Route::get('/categories/{category}/edit', Admin\Categories\CategoryForm::class)->name('categories.edit');
    Route::get('/resellers', Admin\Resellers\ResellerIndex::class)->name('resellers.index');
    Route::get('/resellers/create', Admin\Resellers\ResellerForm::class)->name('resellers.create');
    Route::get('/resellers/{reseller}/edit', Admin\Resellers\ResellerForm::class)->name('resellers.edit');
    Route::get('/articles', Admin\Articles\ArticleIndex::class)->name('articles.index');
    Route::get('/articles/create', Admin\Articles\ArticleForm::class)->name('articles.create');
    Route::get('/articles/{article}/edit', Admin\Articles\ArticleForm::class)->name('articles.edit');
    Route::get('/slides', Admin\Slides\SlideIndex::class)->name('slides.index');
    Route::get('/inquiries', Admin\Inquiries\InquiryIndex::class)->name('inquiries.index');
});

Route::middleware(['auth', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/settings', Admin\Settings\SettingsForm::class)->name('settings');
    Route::get('/users', Admin\Users\UserIndex::class)->name('users.index');
});
```

---

## 13. B2C Patterns & Conversion Optimization

### Trust signals (above the fold)
- "Official distributor of HENEX China" badge
- Warranty seal: "1-2 years official warranty"
- Phone number prominent in header (clickable tel: link)
- WhatsApp/Telegram chat button (fixed bottom-right)

### Product page conversion
- Primary CTA: **"Request a Quote"** (opens Livewire modal)
- Secondary CTA: **"Find where to buy"** (scroll to reseller section)
- Sticky sidebar on desktop: quote form always visible
- Social proof: industry application tags (Retail / Warehousing / Medical)
- Urgency: "In stock in Tashkent" badge (toggle in admin)

### Where to Buy — UX
- Default to user's detected region (IP geolocation via `stevebauman/location`)
- All 14 region tabs visible, active region highlighted
- Each reseller card shows distance from center (static, set in admin)
- "Is there no reseller in your region? Contact us →"

### Inquiry flow
- Every product page has a quote form
- Admin gets email + Telegram notification (via `irazasyed/telegram-bot-sdk`)
- Inquiry list in Filament with "mark as read" and response notes

---

## 14. Deployment Checklist

```
Server: Ubuntu 22.04 / nginx / PHP 8.3 / MySQL 8 / Redis
SSL: Let's Encrypt (certbot)
Queue: supervisor + Laravel queue worker (database or redis driver)

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan icons:cache        # Filament
php artisan optimize

Cron (crontab -e):
* * * * * cd /var/www/henex-uz && php artisan schedule:run >> /dev/null 2>&1

Scheduled commands:
- daily:  php artisan sitemap:generate
- hourly: php artisan queue:prune-batches

nginx config:
- gzip on
- Cache-Control: max-age=31536000 for /build/ assets
- try_files $uri $uri/ /index.php?$query_string
```

---

## 15. Phase Plan

| Phase | Scope | Est. time |
|---|---|---|
| 1 — Foundation | Migrations, models, seeders, Filament resources, basic CRUD | 3–4 days |
| 2 — Frontend | Layouts, homepage sections, product catalog, product detail | 4–5 days |
| 3 — Resellers | ResellerFinder component, region seeder, Yandex Maps embed | 1–2 days |
| 4 — Content | Articles, contact form, inquiry notifications | 1–2 days |
| 5 — SEO | Meta tags, hreflang, JSON-LD, sitemap, WebP images | 1–2 days |
| 6 — Polish | Mobile responsiveness, CWV optimization, admin UX, translations | 2–3 days |
| 7 — Deploy | Server setup, SSL, queue, cron, smoke test | 1 day |
| **Total** | | **~3 weeks** |
