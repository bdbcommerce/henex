# Henex.uz SEO Implementation Guide

Complete SEO optimization setup for Google and Yandex with multilingual support (Uzbek, Russian, English).

---

## ✅ What's Already Implemented

### 1. **Meta Tags & Open Graph**
- ✅ Title tags with fallback
- ✅ Meta descriptions (truncated to 160 chars)
- ✅ Open Graph tags (OG:title, OG:description, OG:image, OG:url)
- ✅ Twitter Card tags
- ✅ Canonical URLs
- ✅ Robots meta tag (index, follow)
- ✅ Theme color meta tag
- ✅ Viewport for mobile optimization
- **Location:** `resources/views/partials/seo-meta.blade.php`

### 2. **Hreflang Tags (Multilingual SEO)**
- ✅ Language alternates for all 3 locales (uz, ru, en)
- ✅ x-default hreflang pointing to Uzbek (primary locale)
- ✅ All URLs locale-aware via mcamara/laravel-localization
- **Impact:** Tells Google/Yandex which content is for which language

### 3. **Structured Data (JSON-LD)**
- ✅ Organization schema (in SEO meta partial)
- ✅ Product schema helper (`SeoHelper::jsonLdProduct()`)
- ✅ Article/NewsArticle schema helper (`SeoHelper::jsonLdArticle()`)
- ✅ LocalBusiness schema helper (`SeoHelper::jsonLdLocalBusiness()`)
- **Location:** `app/Helpers/SeoHelper.php`
- **How to use in controllers:**
  ```php
  <script type="application/ld+json">
    {!! json_encode(SeoHelper::jsonLdProduct($product)) !!}
  </script>
  ```

### 4. **XML Sitemap**
- ✅ Dynamic sitemap generation for all locales
- ✅ Priorities assigned (homepage 1.0, products 0.7, articles 0.6)
- ✅ Change frequency tags (daily, weekly, monthly)
- ✅ Last modification dates
- ✅ Accessible at `/sitemap.xml`
- **Location:** `app/Http/Controllers/SitemapController.php`
- **Cron job needed:** Schedule `php artisan sitemap:generate` daily

### 5. **robots.txt**
- ✅ User-agent rules for Google, Yandex, Bing, DuckDuckGo
- ✅ Disallows `/admin`, `/api`, `/storage` from crawlers
- ✅ Sitemap reference
- ✅ Yandex-specific crawl-delay
- **Location:** `public/robots.txt`

### 6. **Analytics & Search Console**
- ✅ Google Analytics 4 tracking code ready
- ✅ Yandex Metrica tracking code ready
- ✅ Placeholders for verification codes
- **Location:** `resources/views/partials/seo-meta.blade.php`

---

## 🔧 What You Need to Do

### 1. **Update Tracking IDs**
Replace placeholders in `resources/views/partials/seo-meta.blade.php`:

```blade
<!-- Line 34: Google Analytics 4 -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
<!-- Change YOUR_GA4_ID_HERE → G-XXXXXXXXXX -->

<!-- Line 42: Yandex Metrica ID -->
ym(12345678, "init", { ... });  <!-- Change YOUR_YANDEX_METRICA_ID → your ID -->
```

### 2. **Add Search Console Verification**
In `resources/views/partials/seo-meta.blade.php`:

```blade
<!-- Google Search Console -->
<meta name="google-site-verification" content="YOUR_GOOGLE_CODE">

<!-- Yandex Webmaster (Yandex.Webmaster) -->
<meta name="yandex-verification" content="YOUR_YANDEX_CODE">
```

Get codes from:
- **Google Search Console:** https://search.google.com/search-console
- **Yandex Webmaster:** https://webmaster.yandex.com

### 3. **Set Up Cron Job for Sitemap**
Add to crontab on production server:
```bash
0 2 * * * cd /var/www/henex-uz && php artisan sitemap:generate >> /dev/null 2>&1
```

### 4. **Add Page Meta Tags in Controllers**
Example for ProductController:
```php
public function show(Product $product): View
{
    $product->loadMissing('categories', 'specifications');
    
    SeoHelper::setMetaTags(
        title: $product->getTranslation('meta_title', app()->getLocale()) 
               ?? $product->getTranslation('name', app()->getLocale()),
        description: $product->getTranslation('meta_description', app()->getLocale()) 
                     ?? $product->getTranslation('short_description', app()->getLocale()),
        image: $product->getFirstMediaUrl('gallery'),
        url: route('products.show', $product)
    );
    
    return view('pages.products.show', compact('product'));
}
```

### 5. **Add JSON-LD to Page Templates**
In `pages/products/show.blade.php`:
```blade
<script type="application/ld+json">
{!! json_encode(SeoHelper::jsonLdProduct($product)) !!}
</script>
```

In `pages/news/show.blade.php`:
```blade
<script type="application/ld+json">
{!! json_encode(SeoHelper::jsonLdArticle($article)) !!}
</script>
```

---

## 📊 SEO Keywords by Language

### Uzbek (Primary)
- shtrix-kod skaneri
- barcode scanner Toshkent
- HENEX Oʻzbekiston
- barkod skaneri narxi
- skaneri sotib olish

### Russian
- сканер штрих-кода
- штрих-код сканер Ташкент
- HENEX Узбекистан
- сканер штрих-кода цена
- купить сканер штрих-кода

### English
- barcode scanner Uzbekistan
- HENEX Uzbekistan distributor
- POS barcode scanner
- industrial barcode scanner
- wireless barcode scanner

---

## 🎯 SEO Optimization Checklist

### On-Page SEO
- [ ] Title tags (50-60 chars) with primary keyword
- [ ] Meta descriptions (150-160 chars)
- [ ] H1 tags (one per page)
- [ ] H2/H3 hierarchical headings
- [ ] Image alt text (descriptive, keyword-aware)
- [ ] Internal linking (breadcrumbs, related products)
- [ ] URL structure (slugs are SEO-friendly)
- [ ] Page speed (Core Web Vitals)

### Technical SEO
- [ ] Mobile responsiveness ✅ (Tailwind responsive)
- [ ] SSL/HTTPS ✅ (set up with Let's Encrypt)
- [ ] Sitemap.xml ✅ (generated)
- [ ] robots.txt ✅ (configured)
- [ ] Structured data ✅ (JSON-LD schemas)
- [ ] Hreflang tags ✅ (multilingual)
- [ ] Canonical URLs ✅ (in layout)
- [ ] Meta robots ✅ (index, follow)

### Off-Page SEO
- [ ] Submit sitemap to Google Search Console
- [ ] Submit sitemap to Yandex Webmaster
- [ ] Create local citations (Google My Business, Yandex Maps)
- [ ] Build backlinks from local Uzbek directories
- [ ] Social media links (included in schema)

### Content SEO
- [ ] Unique product descriptions (translated for each locale)
- [ ] Blog posts (100+ articles for long-tail keywords)
- [ ] FAQ sections on product pages
- [ ] Category descriptions with keywords
- [ ] Regular content updates

---

## 🌐 Yandex-Specific Setup

### Yandex Webmaster
1. Add property: https://webmaster.yandex.com
2. Verify with meta tag or file upload
3. Submit sitemap
4. Check for crawl errors
5. Monitor indexing status

### Yandex Search Console
- Monitor impressions & clicks
- Check search queries from UZ users
- Set preferred domain (www vs non-www)
- Add hreflang language pairs

### Yandex Metrica (Analytics)
- Setup ID in `seo-meta.blade.php`
- Track goal conversions (quote requests)
- Monitor user flows
- Heat map analysis (click tracking)
- Session recordings

---

## 🔍 Google-Specific Setup

### Google Search Console
1. Add property: https://search.google.com/search-console
2. Verify with meta tag or Domain Name System (DNS)
3. Submit sitemap
4. Request indexing for important pages
5. Monitor Core Web Vitals

### Google Analytics 4
- Setup ID in `seo-meta.blade.php`
- Create custom events for:
  - Quote request submission
  - Product page views
  - Add to cart (future)
  - Filter interactions
- Create audiences for remarketing

### Google My Business
- Create local listings for Tashkent office
- Add categories: "Barcode Scanner Distributor"
- Post regular updates
- Manage customer reviews

---

## 📱 Core Web Vitals Checklist

Already optimized:
- ✅ WebP image format (intervention/image-laravel)
- ✅ Image lazy loading
- ✅ CSS/JS minification (Vite)
- ✅ Font optimization (Inter from Bunny fonts)
- ✅ Responsive design (Tailwind)

Need to monitor:
- [ ] Largest Contentful Paint (LCP) < 2.5s
- [ ] First Input Delay (FID) < 100ms
- [ ] Cumulative Layout Shift (CLS) < 0.1
- [ ] Page load time < 3s

---

## 🚀 Quick Start Commands

```bash
# Generate sitemap
php artisan sitemap:generate

# Clear caches
php artisan cache:clear
php artisan view:clear

# Monitor crawl errors
# Google: search.google.com/search-console
# Yandex: webmaster.yandex.com

# Test structured data
# Google Rich Results Test: https://search.google.com/test/rich-results
# Yandex Structured Data Validator: https://webmaster.yandex.com/tools/validator/
```

---

## 📚 Resources

- **Google Search Central:** https://developers.google.com/search
- **Yandex Webmaster Help:** https://yandex.com/support/webmaster
- **Schema.org:** https://schema.org
- **SEO Checklist:** https://www.mailchimp.com/resources/seo-checklist/

---

**Last Updated:** June 25, 2026  
**Status:** Ready for production deployment
