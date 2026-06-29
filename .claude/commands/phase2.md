Implement Phase 2 of the Henex.uz project: Public Frontend.

Phase 2 scope (from CLAUDE.md section 15):
- Public site layout (layouts/app.blade.php)
- Homepage with all 8 sections
- Product catalog page with Livewire filtering
- Product detail page with specs, gallery, quote form
- Locale switcher component
- Breadcrumb component
- Product card component

Work through these in order:

**Step 1 — App layout** (resources/views/layouts/app.blade.php)
Sticky header: logo + nav + locale switcher + phone CTA.
Footer: address, phone, social links, region links.
Include `@livewireStyles` / `@livewireScripts`.
Set `lang="{{ app()->getLocale() }}"` on <html>.
Include hreflang tags via mcamara getLocalizedURL().

**Step 2 — Homepage sections** (resources/views/pages/home.blade.php)
Render all 8 sections in order:
HeroSlider → FeaturedCategories → FeaturedProducts → IndustryApplications → WhyHenex → ResellerCTA → LatestNews → ContactStrip

**Step 3 — Livewire: HeroSlider**
Auto-advancing slider using Alpine.js x-data setInterval.
Pulls active slides from DB with MediaLibrary image.

**Step 4 — Livewire: ProductCatalog**
$search (debounce 300ms), $categorySlug, $sort, pagination.
Category sidebar tree, grid/list toggle, URL-synced with #[Url].

**Step 5 — Product detail page**
Swiper.js gallery (CDN), specs table, PDF download links.
ProductQuoteForm Livewire modal → saves to inquiries.

**Step 6 — Components**
product-card.blade.php, breadcrumb.blade.php, locale-switcher.blade.php

Use Tailwind brand colors (brand red #E63329) and Inter font throughout.
Test all three locales (uz/ru/en) for each page.
