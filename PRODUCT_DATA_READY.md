# 📦 Product Data Ready for Import

## Summary

**67 HENEX products** with complete metadata and image URLs are ready to import into the database.

### Data Source
- **henex.cn** product catalog (official HENEX China website)
- **467 high-quality product images** (main + gallery per product)
- **Full product specifications** (features, applications, categories)
- **Multi-language ready** (can be localized to uz/ru/en)

---

## What's Ready

### ✅ Metadata Structure
Each product includes:
```
├── Product Details
│   ├── ID (SKU): 202, 201, 77, etc.
│   ├── Name: H310RC, HC-3206R, etc.
│   ├── Subtitle: "2D Wireless Barcode Scanner"
│   ├── Category: "Wireless Barcode Scanner"
│   ├── Subcategory: "2D Wireless Barcode Scanner"
│   ├── Features: Technical specifications
│   └── Applications: Industry use cases
│
└── Images (7 per product)
    ├── 1 Main product image
    └── 6 Gallery/detail images
```

### ✅ File Organization
```
.claude/store/
├── index.json                    # Master product list
├── H310RC/metadata.json         # Product #1
├── H310R/metadata.json          # Product #2
├── HC-3206R/metadata.json       # Product #3
└── ... (64 more products)
```

### ✅ Import Command Ready
```bash
php artisan import:henex-products
```

Automatically:
- Creates 67 products in database
- Sets up multi-locale translations (uz/ru/en)
- Assigns categories
- Downloads & attaches images
- Converts images to WebP
- Generates responsive srcsets

---

## Product Categories

**8 main categories** from HENEX.cn:

1. **Desktop Barcode Scanners** (7 products)
   - HC-666, HC-7060, HC-6052, H2200, H2210, H8000, HC-8288

2. **Wired Barcode Scanners** (21 products)
   - 1D wired: HC-2000, HC-2102, HC-3206 (laser/CCD)
   - 2D wired: H310, H3308 (S/SR variants), HC-3208, HC-5208, HC-3206, HC-3209, HC-4208 (D/S/SR/RC/RCS variants)
   - Industrial: H900, HC-4208D

3. **Wireless Barcode Scanners** (35 products)
   - 1D wireless: HC-2000R, HC-2102R, HC-3206R (laser/CCD)
   - 2D wireless: H300R (and variants with S/D/RD/SRD), H3308R (and variants), HC-3208R (and variants), HC-5208R (and variants), HC-3209R (and variants), HC-4208R (and variants), H900R, H310RC, H310R
   - Wearable: H500

4. **Barcode Scanner Modules** (6 products)
   - H60, H33, H30, H22, H1210, H1203

5. **Mobile Data Collectors** (2 products)
   - H5, H4

**Plus:** POS Terminals, Industrial Scanners (sub-categories)

---

## Product Examples

### H310RC — 2D Wireless Barcode Scanner
```
SKU: 202
Category: Wireless Barcode Scanner
Features:
  - 3-in-1 transmission: Cradle + BT + Type-C Cable
  - Support Charge cradle Or USB Dongle (optional)
  - Automatic charge in cradle, one key pair and one key upload data
  - One cradle with one scanner, or one cradle with more scanners (optional)
  - Support double firmware upgrade online: wireless and decoder
  - Support for offline storage mode, can memory 380,000 characters (EAN-13 code)

Applications:
  Logistics, express, warehouse inventory, tobacco, health care, industrial business

Images: 7 (1 main + 6 gallery)
```

### H500 — 2D Wearable Scanner
```
SKU: 97
Category: Wireless Barcode Scanner
Features:
  - 1.0MP CMOS image 1280×800px, quickly reads 1D and 2D codes
  - Support software online upgrade
  - Support finger, glove and lanyard wearing
  - Support Bluetooth and 2.4G mode
  - Shortcut pairing: scan code pairing and NFC touch
  - 600mAh removable lithium battery
  - Host and battery can be charged independently by cradle or cable

Applications:
  Business, warehousing, express delivery, industrial use
```

---

## Import Quick Start

### Step 1: Create Categories (if not exists)
```bash
php artisan db:seed --class=CategorySeeder
```

Or manually in admin panel:
```
/admin/categories → + Add New
```

### Step 2: Run Import
```bash
php artisan import:henex-products
```

**Output:**
```
✅ Importing H310RC (202)...
✅ Importing H310R (201)...
...
✅ Import complete!
Imported: 67 products
Skipped: 0 products
```

### Step 3: Verify
```bash
php artisan tinker
>>> Product::count()  # Should show 67
>>> Product::first()->media()->count()  # Should show 6-7 images
```

### Step 4: Check Frontend
Visit `/products` to see all products with galleries.

---

## Technical Details

### Database Schema
```sql
-- Products
CREATE TABLE products (
  id BIGINT PRIMARY KEY
  sku VARCHAR (60) UNIQUE           -- Henex product ID (202, 201, 77, etc.)
  slug VARCHAR (180) UNIQUE         -- h310rc, h310r, hc-3206r
  name JSON                         -- {"uz":"...","ru":"...","en":"..."}
  short_description JSON
  description JSON                  -- HTML: features + applications
  is_active BOOLEAN                 -- true (all imported)
  is_featured BOOLEAN               -- false (set manually)
  sort_order SMALLINT               -- 0 (set manually)
  created_at TIMESTAMP
  updated_at TIMESTAMP
)

-- Category relationships
CREATE TABLE category_product (
  category_id BIGINT
  product_id BIGINT
  PRIMARY KEY (category_id, product_id)
)

-- Media (images)
CREATE TABLE media (
  id BIGINT PRIMARY KEY
  model_id BIGINT                   -- product ID
  model_type VARCHAR (255)          -- 'App\Models\Product'
  collection_name VARCHAR (255)     -- 'gallery'
  name VARCHAR (255)                -- main_8ce4a58e904e519
  file_name VARCHAR (255)           -- 8ce4a58e904e519.jpg
  mime_type VARCHAR (255)           -- image/jpeg
  disk VARCHAR (255)                -- local
  size BIGINT                       -- bytes
  created_at TIMESTAMP
  updated_at TIMESTAMP
)
```

### Image Processing
- **Format:** JPG → JPG + WebP (auto-conversion)
- **Sizes:** 400w, 800w, 1200w (responsive srcset)
- **Storage:** `storage/app/media/{product_id}/conversions/`
- **CDN-ready:** Nginx can serve WebP with Accept header fallback

### Localization
- **Default:** `en` (English)
- **Supported:** `uz` (Uzbek), `ru` (Russian), `en` (English)
- **Fallback:** English translations used for uz/ru (can be manually translated later)

---

## File Locations

```
Project
├── .claude/
│   ├── henex.cn/
│   │   └── products_detail.json    # Original data
│   ├── store/
│   │   ├── index.json              # Master index
│   │   ├── H310RC/
│   │   │   └── metadata.json
│   │   └── ... (67 product folders)
│   └── IMPORT_GUIDE.md             # Full import documentation
│
├── app/Console/Commands/
│   └── ImportHenexProducts.php     # Import command
│
├── database/seeders/
│   └── CategorySeeder.php          # Creates 8 categories
│
├── storage/app/media/              # Downloaded images
│   └── {product_id}/
│       ├── conversions/
│       │   ├── *.jpg
│       │   └── *.webp
│       └── ...
│
└── public/storage/ → storage/app/  # Public symlink
```

---

## Next Phase Tasks

After import:

- [ ] Verify 67 products in database
- [ ] Check image galleries on product pages
- [ ] Mark 8–12 products as "featured" for homepage
- [ ] Add product specifications (technical details)
- [ ] Write SEO meta titles/descriptions
- [ ] Add category descriptions
- [ ] Test product filters & search
- [ ] Optimize images for performance
- [ ] Set up reseller listings by region

---

## Statistics

| Metric | Count |
|--------|-------|
| Products | 67 |
| Categories | 8 |
| Images | 469 (avg 7 per product) |
| Locales | 3 (uz, ru, en) |
| Data Size | ~2–3 MB (metadata only) |
| Image Size | ~150–200 MB (after import) |

---

## Resources

- 📄 [IMPORT_GUIDE.md](.claude/IMPORT_GUIDE.md) — Detailed import instructions
- 📦 [.claude/store](.claude/store/) — Product metadata & image URLs
- 💻 [ImportHenexProducts.php](app/Console/Commands/ImportHenexProducts.php) — Import command code
- 🌐 [henex.cn](https://www.henex.cn) — Source data (live product pages)

---

**Status:** ✅ Ready to Import  
**Date:** 2026-06-26  
**Updated:** 2026-06-26
