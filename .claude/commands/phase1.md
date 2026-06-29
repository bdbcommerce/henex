Implement Phase 1 of the Henex.uz project: Foundation layer.

Phase 1 scope (from CLAUDE.md section 15):
- All database migrations (10 tables)
- All Eloquent models with traits and relationships
- Database seeders (RegionSeeder + CategorySeeder)
- Admin panel routes + middleware
- Basic Livewire admin Index + Form components for all entities
- Settings helper class

Work through these in order:

**Step 1 — Migrations** (database/migrations/)
Create migrations for: regions, categories, products, category_product, product_specifications, resellers, articles, slides, inquiries, settings.
Follow the exact schema from CLAUDE.md section 3.

**Step 2 — Models** (app/Models/)
Create: Region, Category, Product, ProductSpecification, Reseller, Article, Slide, Inquiry, Setting.
Add HasTranslations where specified. Add HasMedia where specified.
Add Settings helper at app/Helpers/Settings.php.

**Step 3 — Seeders**
RegionSeeder: all 14 UZ regions with uz/ru/en names and slugs.
CategorySeeder: 8 Henex product categories with uz/ru/en names.

**Step 4 — Routes**
Create routes/admin.php. Include it in routes/web.php.
Add locale-prefixed public routes using mcamara/laravel-localization.

**Step 5 — Admin Livewire components** (app/Livewire/Admin/)
Stub out all Index and Form components. They don't need full logic yet — just the class structure, mount(), and render() returning the correct view.

After completing, run `php artisan migrate --seed` to verify everything works.
