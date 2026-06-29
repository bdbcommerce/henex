# Henex.uz - Deployment & Launch Checklist

**Project Status:** Phase 2 Frontend Complete | Ready for Database Setup  
**Date:** June 25, 2026  
**Stack:** Laravel 13 · Livewire 4 · Tailwind CSS 4 · MySQL 8 · PHP 8.3

---

## 🎯 Current Status

### ✅ Complete (0-Day Ready)
1. **Laravel Foundation**
   - Project initialized with Laravel 13.17
   - All required packages installed (Livewire, Spatie ecosystem, etc.)
   - Environment configured (except DB credentials in .env need verification)

2. **Database Layer**
   - 10 migrations created with full schema
   - 9 models with relationships, traits, media collections
   - RegionSeeder (14 Uzbekistan regions) + CategorySeeder (8 Henex categories)
   - All translatable fields configured (uz/ru/en)

3. **Routing & Controllers**
   - Public routes with locale-prefixed URLs (/{locale}/products, etc.)
   - Admin routes with auth + role-based protection
   - 7 Controllers stubbed (Home, Product, Article, Reseller, Contact, StaticPage, Sitemap)

4. **Frontend Foundation**
   - Public layout with sticky header, responsive nav, footer
   - Admin layout with sidebar + topbar
   - Homepage with 8 sections (hero, trust badges, categories, products, news, CTA)
   - Language files for Uzbek, Russian, English

5. **Styling & Theme**
   - Tailwind CSS 4 configured with Henex colors (#E63329 brand red)
   - Inter font (Cyrillic-safe typography)
   - Responsive design across all layouts
   - Dark theme admin panel

6. **SEO Optimization**
   - Meta tags partial (title, description, OG tags, Twitter cards)
   - Hreflang tags for multilingual URLs
   - Sitemap controller (dynamic XML generation)
   - robots.txt with Google/Yandex/Bing rules
   - JSON-LD schema helpers (Organization, Product, Article, LocalBusiness)
   - Google Analytics 4 & Yandex Metrica tracking ready
   - Comprehensive SEO_GUIDE.md

---

## ⏳ Next Steps (Blocking on MySQL)

### Step 1: Start MySQL Server
- Open OSPanel control panel
- Start **MySQL 8.4** or **MariaDB 11.7**
- Verify connection: `mysql -u root -e "SELECT 1"`

### Step 2: Create Database & Configure
```bash
# Create database with UTF-8 collation
mysql -u root -e "CREATE DATABASE IF NOT EXISTS henex_uz CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Verify
mysql -u root -e "SHOW DATABASES LIKE 'henex%';"
```

**CRITICAL:** Verify `.env` has correct values:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1      ← NOT "MySQL-8.4"
DB_PORT=3306
DB_DATABASE=henex_uz   ← NOT "henex"
DB_USERNAME=root
DB_PASSWORD=           ← Blank for OSPanel
```

### Step 3: Publish Vendor Configs
```bash
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-migrations"
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Mcamara\LaravelLocalization\LaravelLocalizationServiceProvider"
```

### Step 4: Run Migrations
```bash
php artisan migrate
```
Expected output: 15+ migrations run successfully

### Step 5: Seed Database
```bash
php artisan db:seed
```
Expected: 14 regions + 8 categories inserted

### Step 6: Build Frontend Assets
```bash
npm install
npm run build
```

---

## 📋 Files Summary

### Core Application
```
app/
├── Http/Controllers/      ✅ 7 controllers (stubs ready)
├── Livewire/Admin/        ⏳ Pending (dashboard, CRUD components)
├── Livewire/Frontend/     ⏳ Pending (ProductCatalog, etc.)
├── Models/                ✅ 9 models with relationships
├── Helpers/
│   └── SeoHelper.php      ✅ JSON-LD schema generators
└── Console/Commands/      ⏳ Sitemap generation command

database/
├── migrations/            ✅ 10 migrations (regions, categories, products, etc.)
└── seeders/               ✅ RegionSeeder, CategorySeeder

routes/
└── web.php                ✅ Public + admin routes

resources/
├── views/
│   ├── layouts/
│   │   ├── app.blade.php       ✅ Public site layout
│   │   └── admin.blade.php     ✅ Admin layout
│   ├── pages/
│   │   └── home.blade.php      ✅ Homepage
│   ├── partials/
│   │   └── seo-meta.blade.php  ✅ SEO meta tags
│   └── livewire/               ⏳ Component views (pending)
├── css/app.css            ✅ Tailwind theme configured
└── js/app.js              ✅ Livewire integration

lang/
├── uz/site.php            ✅ Uzbek translations
├── ru/site.php            ✅ Russian translations
└── en/site.php            ✅ English translations

public/
└── robots.txt             ✅ Search engine directives
```

---

## 🔐 Security Checklist

- [ ] Change default `APP_KEY` (already generated)
- [ ] Set `APP_DEBUG=false` in production
- [ ] Configure HTTPS/SSL (Let's Encrypt)
- [ ] Set strong database password
- [ ] Configure firewall rules
- [ ] Enable CSRF protection (default)
- [ ] Use authenticated routes for admin (`auth` middleware)
- [ ] Role-based access control (Spatie Permission)
- [ ] Sanitize user input in forms
- [ ] Rate limiting on contact/quote forms

---

## 📊 Analytics Integration

### Before Launch:
1. **Google Search Console**
   - Visit: https://search.google.com/search-console
   - Add property: `henex.uz`
   - Verify with meta tag from `seo-meta.blade.php`
   - Add sitemap: `/sitemap.xml`

2. **Yandex Webmaster**
   - Visit: https://webmaster.yandex.com
   - Add property: `henex.uz`
   - Verify with meta tag or DNS
   - Add sitemap: `/sitemap.xml`
   - Configure for `.uz` domain

3. **Google Analytics 4**
   - Create property in Google Analytics
   - Get tracking ID (format: `G-XXXXXXXXXX`)
   - Add to `seo-meta.blade.php` (line 34)

4. **Yandex Metrica**
   - Visit: https://metrica.yandex.com
   - Create account for `henex.uz`
   - Get counter ID
   - Add to `seo-meta.blade.php` (line 43)

---

## 🚀 Production Server Setup

**Recommended:** Ubuntu 22.04 + Nginx + PHP 8.3 + MySQL 8 + Redis

```bash
# PHP extensions
php-mysql php-xml php-curl php-mbstring php-tokenizer php-json

# Cron job for scheduler
* * * * * cd /var/www/henex-uz && php artisan schedule:run >> /dev/null 2>&1

# Supervisor for queue worker
[program:henex-queue-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/henex-uz/artisan queue:work --queue=default --tries=3
...

# Cache and config
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

---

## 📱 Testing Before Launch

### Homepage
- [ ] All 8 sections load correctly
- [ ] Locale switcher works (uz/ru/en)
- [ ] Trust badges display
- [ ] Featured categories visible
- [ ] Featured products show (once seeded)
- [ ] Latest news displays (once articles created)
- [ ] CTA buttons functional
- [ ] Footer links work

### Product Pages
- [ ] Category filter works
- [ ] Product detail page loads
- [ ] Images display correctly
- [ ] Specs table shows
- [ ] Request quote button works (Livewire form)

### Admin Panel
- [ ] Dashboard loads
- [ ] Product CRUD works
- [ ] Image upload/media works
- [ ] Translations saved correctly
- [ ] Reseller management works

### SEO
- [ ] Sitemap generates (`/sitemap.xml`)
- [ ] robots.txt accessible
- [ ] JSON-LD renders correctly (test with Google Rich Results)
- [ ] Hreflang tags present
- [ ] Canonical URLs correct
- [ ] OG tags render for social sharing

---

## 🎯 Future Phase 3: Advanced Features

1. **Remaining Livewire Components**
   - ProductCatalog (with filters, search, pagination)
   - ResellerFinder (region tabs, map integration)
   - ContactForm (with validation, email notifications)
   - QuoteForm (modal, product linking)
   - Admin CRUD forms for all entities

2. **Advanced Features**
   - Yandex Maps integration (reseller locations)
   - Rich text editor for articles (TipTap or Quill)
   - Image optimization & WebP conversion
   - Email notifications (queue-based)
   - Admin dashboard stats
   - Inquiry management with responses

3. **Performance**
   - Page caching strategy
   - Image CDN integration
   - Database query optimization
   - Redis caching layer

---

## 📞 Support & Resources

- **CLAUDE.md** — Complete project specification
- **SEO_GUIDE.md** — Comprehensive SEO checklist
- **Laravel Docs** — https://laravel.com/docs
- **Livewire Docs** — https://livewire.laravel.com
- **Tailwind CSS** — https://tailwindcss.com/docs

---

**Next Action:** Start MySQL → Run migrations → Test homepage

**Estimated time to launch:** 2-3 weeks (including Livewire components, content creation, testing)
