Open Laravel Tinker to run PHP interactively against the application.

```bash
php artisan tinker
```

Useful one-liners for this project:
- `App\Models\Product::count()` — count products
- `App\Models\Region::all()->pluck('name')` — list all regions
- `App\Helpers\Settings::get('site_phone')` — get a setting value
- `App\Helpers\Settings::set('site_phone', '+998 90 123 45 67')` — set a value
- `App\Models\User::first()->assignRole('super_admin')` — assign admin role
