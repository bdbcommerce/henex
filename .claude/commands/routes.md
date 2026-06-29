List all registered application routes grouped by prefix.

```bash
php artisan route:list --except-vendor
```

Verify the following routes exist as specified in CLAUDE.md:
- `/{locale}` → HomeController (uz, ru, en)
- `/{locale}/products` → ProductCatalog Livewire
- `/{locale}/products/{slug}` → product detail
- `/{locale}/where-to-buy` → ResellerFinder Livewire
- `/{locale}/news` and `/{locale}/news/{slug}`
- `/{locale}/contact` → ContactForm Livewire
- `/admin/*` routes protected by auth + role middleware
