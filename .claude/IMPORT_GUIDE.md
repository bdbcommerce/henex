# HENEX Product Import Guide

## Overview

The `.claude/store` directory contains metadata for 67 HENEX barcode scanner products from henex.cn with links to 469 product images.

## Structure

```
.claude/
├── store/
│   ├── index.json                    # Master index of all products
│   ├── H310RC/
│   │   ├── metadata.json             # Product details + image URLs
│   │   └── gallery_1_*.jpg           # Downloaded images (after import)
│   ├── H310R/
│   │   └── metadata.json
│   ├── HC-3206R/
│   │   └── metadata.json
│   └── ... (65 more products)
```

## What's Included

Each product folder contains:

**metadata.json**
```json
{
  "id": 202,
  "name": "H310RC",
  "subtitle": "2D Wireless Barcode Scanner",
  "category": "Wireless Barcode Scanner",
  "subcategory": "2D Wireless Barcode Scanner",
  "url": "https://www.henex.cn/index.php?c=show&id=202",
  "features": "3-in-1 transmission: Cradle + BT + Type-C Cable ...",
  "application": "Logistics, express, warehouse inventory, tobacco, health care, industrial business",
  "images": [
    {
      "url": "https://www.henex.cn/uploadfile/202509/8ce4a58e904e519.jpg",
      "filename": "main_8ce4a58e904e519.jpg",
      "type": "main"
    },
    {
      "url": "https://www.henex.cn/uploadfile/202509/376eb0daec1f525.jpg",
      "filename": "gallery_1_376eb0daec1f525.jpg",
      "type": "gallery"
    }
    // ... more images
  ]
}
```

## Quick Import (Simple)

To import all products with minimal setup:

```bash
cd d:\OSPanel\home\Henex
php artisan import:henex-products
```

This will:
1. Read metadata from each product folder
2. Create products in database with all 3 locales (uz/ru/en)
3. Assign categories (if they exist)
4. Download images from henex.cn to `.claude/store/{product}/`
5. Attach images to product media library

**Time:** ~5-10 minutes (depends on internet speed)

## Import with Image Caching (Recommended)

For faster re-imports or batch operations:

```bash
# First run: download and cache images locally
php artisan import:henex-products

# Subsequent runs: use cached images (no downloads)
php artisan import:henex-products --skip-images
```

## Advanced: Selective Import

To import specific products, edit the command or use Laravel Tinker:

```bash
php artisan tinker

# Import single product
$metadata = json_decode(file_get_contents('.claude/store/H310RC/metadata.json'), true);
// Create product...
```

## Product Categories to Create First

Before importing products, ensure these categories exist (or they'll be skipped):

- Desktop Barcode Scanners
- Wired Barcode Scanners
- Wireless Barcode Scanners
- Wearable Barcode Scanners
- Industrial Barcode Scanners
- Barcode Scanner Modules
- Mobile Data Collectors
- POS Terminals

Create them in `/admin/categories` or via seeder:

```php
// database/seeders/CategorySeeder.php
Category::create([
    'slug' => 'wireless-barcode-scanners',
    'name' => [
        'uz' => "Simsiz shtrix-kod skanerlari",
        'ru' => "Беспроводные сканеры штрих-кодов",
        'en' => "Wireless Barcode Scanners"
    ],
    'is_active' => true,
]);
```

## What Gets Created

### Products Table
- **SKU:** Henex product ID (e.g., "202" for H310RC)
- **Slug:** URL-friendly name (e.g., "h310rc")
- **Name:** Multi-locale (uz/ru/en)
- **Short Description:** Subtitle from henex.cn
- **Description:** Formatted HTML with features + applications
- **Active:** Yes (all imported as active)
- **Featured:** No (set manually in admin)

### Media Library
- Images stored in `storage/app/media/{product_id}/`
- Multiple formats generated (original, thumbnails, WebP)
- Attached to product `gallery` collection

### Categories
- Products linked to category via pivot table
- Based on "category" field from metadata

## Verification

After import, verify data:

```bash
php artisan tinker

# Count products
Product::count()  # Should be ~67

# Check images
Product::first()->media()->count()  # Should be 6-7 images per product

# View products
Product::with('categories')->paginate()
```

## Troubleshooting

### "Store directory not found"
Ensure `.claude/store` exists with metadata.json files:
```bash
ls d:\OSPanel\home\Henex\.claude\store
```

### "Product already exists" (skipped many)
Products with SKU = henex ID are skipped on re-import. To re-import:
```bash
# Delete and re-create
Product::truncate();
php artisan import:henex-products
```

### Image downloads timeout
Henex.cn may be slow. Try with `--skip-images` and use cached images:
```bash
php artisan import:henex-products --skip-images
```

### Categories not assigned
Create categories first with correct slugs. Slugs must match EXACTLY (case-insensitive but same structure):
```php
// Must exist:
'wireless-barcode-scanners'
'wired-barcode-scanners'
'desktop-barcode-scanners'
// etc.
```

## After Import

Once products are in database:

1. **Add to Homepage**
   - Mark some as `is_featured = true`
   - They'll appear in "Featured Products" section

2. **Organize Categories**
   - Group related products
   - Set sort_order for display sequence

3. **Add Specifications**
   - In admin panel, edit each product
   - Add technical specs from feature list

4. **Optimize Images**
   - Images auto-convert to WebP on upload
   - Resize to responsive srcset (400w, 800w, 1200w)

5. **SEO Setup**
   - Meta titles: e.g., "H310RC 2D Wireless Barcode Scanner | Henex Uzbekistan"
   - Meta descriptions from subtitle + features

## File Locations

```
Project Root
├── .claude/store/              # Product metadata & images
├── storage/app/media/          # MediaLibrary files
├── database/seeders/
│   └── CategorySeeder.php      # Create categories first
└── app/Console/Commands/
    └── ImportHenexProducts.php # The import command
```

## Command Reference

```bash
# Display help
php artisan import:henex-products --help

# Run with output
php artisan import:henex-products -vv

# Skip image downloads (use cached)
php artisan import:henex-products --skip-images

# Check progress
php artisan tinker
>>> Product::count()  # See how many were imported
>>> Product::where('sku', '202')->first()  # Check specific product
```

## Next Steps

After successful import:

1. ✅ Verify product count: `Product::count()`
2. ✅ Check image gallery on product detail page
3. ✅ Mark featured products for homepage
4. ✅ Add product specifications in admin
5. ✅ Write SEO meta titles/descriptions
6. ✅ Test on frontend: `/products`

---

**Last Updated:** 2026-06-26  
**Products:** 67 | **Images:** 469  
**Categories:** 8  
**Locales:** 3 (uz, ru, en)
