Drop all tables, re-run all migrations, then seed the database.

```bash
php artisan migrate:fresh --seed
```

Show the full output. After seeding, confirm that the regions (14 UZ regions) and categories (8 Henex product categories) were created successfully.

WARNING: This destroys all existing data. Only run in development.
