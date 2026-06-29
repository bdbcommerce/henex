Run the full project installation sequence as defined in CLAUDE.md section 12.

Steps to execute in order:

1. Install PHP dependencies:
```bash
composer install
```

2. Copy environment file and generate app key:
```bash
cp .env.example .env
php artisan key:generate
```

3. Publish vendor assets and run migrations:
```bash
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-migrations"
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Mcamara\LaravelLocalization\LaravelLocalizationServiceProvider"
php artisan migrate
```

4. Seed the database:
```bash
php artisan db:seed
```

5. Install and build frontend:
```bash
npm install
npm run build
```

After each step, check for errors before proceeding. Report the final status of all steps.

NOTE: Before running, ensure `.env` is configured with the correct DB_DATABASE, DB_USERNAME, and DB_PASSWORD for the local OSPanel MySQL instance.
