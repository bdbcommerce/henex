Run Laravel database seeders.

```bash
php artisan db:seed
```

If $ARGUMENTS is provided, run only that specific seeder class:
```bash
php artisan db:seed --class=$ARGUMENTS
```

Confirm the seeded records after running. For the RegionSeeder, verify all 14 Uzbekistan regions are present. For CategorySeeder, verify all 8 Henex product categories.
