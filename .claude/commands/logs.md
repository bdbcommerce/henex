Show the last 100 lines of the Laravel application log to diagnose errors.

```bash
php artisan pail --timeout=0
```

If `pail` is not installed, fall back to:
```bash
Get-Content storage/logs/laravel.log -Tail 100
```

Look for ERROR and CRITICAL level entries and summarize any issues found.
