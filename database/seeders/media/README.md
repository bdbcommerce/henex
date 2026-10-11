# Seed media

Source images used by the database seeders. Drop files here, reference them by
file name in the matching seeder, then run `php artisan migrate:fresh --seed`.

| Folder        | Used by              | How it is referenced                                          |
|---------------|----------------------|---------------------------------------------------------------|
| `logo/`       | `BrandAssetSeeder`   | `$files` map → copied to `public/images/` (`henex-logo.png`)   |
| `categories/` | `CategorySeeder`     | `'image' => 'wired-barcode-scanners.jpg'` per category          |
| `products/`   | `ProductSeeder`      | `$images['H500'] = ['h500-1.jpg', 'h500-2.jpg', ...]` (first = main image); unlisted products auto-pick `{slug}-1.jpg`, `{slug}-2.jpg`, ... |
| `slides/`     | `SlideSeeder`        | `'image' => 'slide-1.jpg'` per slide (slide skipped if file missing) |

Notes
- Missing files only print a warning — seeding never fails because of an image.
- MediaLibrary stores everything flat in `storage/app/public/images/`, so file
  names must be unique. Categories are saved as `category-<name>`, slides as
  `hero-<name>`; product files are already unique (`<slug>-<n>.jpg`).
- Recommended sizes: categories 600×600, products 1000×1000 (white background),
  slides 1920×700, logo PNG with transparent background.
